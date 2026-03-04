import PDFDocument = require('pdfkit');
import { JobHandler } from '../types';
import * as fs from 'fs';

const PRIMARY_COLOR = '#1565C0';    // Blu qualità
const ACCENT_COLOR  = '#E3F2FD';
const SECONDARY     = '#424242';

const handler: JobHandler = async (payload, helpers) => {
  const { userId, jobId, dataInizio, dataFine, reparto, operatore, tipoCq } = payload as {
    userId: number;
    jobId: string;
    dataInizio?: string;
    dataFine?: string;
    reparto?: string;
    operatore?: string;
    tipoCq?: string;
  };

  const filters = { dataInizio, dataFine, reparto, operatore, tipoCq };
  const fileName = `REPORT_QUALITA_${new Date().toISOString().split('T')[0]}.pdf`;
  const { fullPath } = await helpers.ensureOutputPath(userId, jobId, fileName);

  const buffer = await generatePdf(filters, helpers);
  fs.writeFileSync(fullPath, buffer);

  const stat = fs.statSync(fullPath);
  return {
    outputPath: fullPath,
    outputName: fileName,
    outputMime: 'application/pdf',
    outputSize: Number(stat.size),
  };
};

async function generatePdf(filters: any, helpers: any): Promise<Buffer> {
  return new Promise(async (resolve, reject) => {
    try {
      const { trackingService } = helpers;
      const prisma = (trackingService as any).prisma;

      // ---- Query DB ----
      const where: any = {};
      if (filters.dataInizio || filters.dataFine) {
        where.dataControllo = {};
        if (filters.dataInizio) where.dataControllo.gte = new Date(filters.dataInizio);
        if (filters.dataFine) {
          const endDate = new Date(filters.dataFine);
          endDate.setHours(23, 59, 59, 999);
          where.dataControllo.lte = endDate;
        }
      }
      if (filters.reparto)   where.reparto   = filters.reparto;
      if (filters.operatore) where.operatore = { contains: filters.operatore };
      if (filters.tipoCq)    where.tipoCq    = filters.tipoCq;

      const [records, operators, departments, defectTypes] = await Promise.all([
        prisma.qualityRecord.findMany({ where, include: { exceptions: true }, orderBy: { dataControllo: 'desc' } }),
        prisma.inworkOperator.findMany({ select: { matricola: true, nome: true, cognome: true } }),
        prisma.qualityDepartment.findMany({ select: { id: true, nomeReparto: true } }),
        prisma.qualityDefectType.findMany({ select: { id: true, descrizione: true } }),
      ]);

      const operatorMap = new Map<string, string>(operators.map((o: any) => [o.matricola, `${o.nome} ${o.cognome}`]));
      const departmentMap = new Map<string, string>(departments.map((d: any) => [String(d.id), d.nomeReparto]));
      const defectTypeMap = new Map<string, string>(defectTypes.map((d: any) => [String(d.id), d.descrizione]));

      // ---- Stats ----
      const totalRecords = records.length;
      const withExceptions = records.filter((r: any) => r.haEccezioni).length;
      const ok = totalRecords - withExceptions;
      const successRate = totalRecords > 0 ? (ok / totalRecords) * 100 : 0;

      // By department
      const byDept: Record<string, { total: number; ok: number; exceptions: number }> = {};
      records.forEach((r: any) => {
        const name = r.reparto ? (departmentMap.get(r.reparto) || r.reparto) : 'Non specificato';
        if (!byDept[name]) byDept[name] = { total: 0, ok: 0, exceptions: 0 };
        byDept[name].total++;
        r.haEccezioni ? byDept[name].exceptions++ : byDept[name].ok++;
      });

      // By operator
      const byOp: Record<string, { total: number; ok: number; exceptions: number }> = {};
      records.forEach((r: any) => {
        const name = operatorMap.get(r.operatore) || r.operatore || 'N/D';
        if (!byOp[name]) byOp[name] = { total: 0, ok: 0, exceptions: 0 };
        byOp[name].total++;
        r.haEccezioni ? byOp[name].exceptions++ : byOp[name].ok++;
      });

      // Exception types
      const excTypes: Record<string, number> = {};
      records.forEach((r: any) => {
        r.exceptions?.forEach((exc: any) => {
          const name = defectTypeMap.get(exc.tipoDifetto) || exc.tipoDifetto;
          excTypes[name] = (excTypes[name] || 0) + 1;
        });
      });

      // ---- PDF Document ----
      const doc = new PDFDocument({
        margin: 40,
        size: 'A4',
        layout: 'landscape',
        autoFirstPage: true,
        bufferPages: true,
        info: {
          Title: 'Report Controllo Qualità',
          Author: 'CoreGRE Quality Control System',
          Subject: 'Analisi Qualità',
        },
      });

      const chunks: Buffer[] = [];
      doc.on('data', (c) => chunks.push(c));
      doc.on('end', () => resolve(Buffer.concat(chunks)));
      doc.on('error', reject);

      const pageWidth = doc.page.width - 80;
      const pageHeight = doc.page.height;
      const bottomMargin = 45;
      const usableBottom = pageHeight - bottomMargin;

      const safeText = (text: string, x: number, y: number, opts?: any) =>
        doc.text(text, x, y, { lineBreak: false, ...opts });

      const needsNewPage = (y: number, height: number) => (y + height) > usableBottom;

      const drawTableHeader = (headers: string[], colWidths: number[], startY: number, totalW: number) => {
        doc.rect(40, startY, totalW, 22).fill(PRIMARY_COLOR);
        doc.fillColor('#FFFFFF').fontSize(8).font('Helvetica-Bold');
        let hx = 45;
        headers.forEach((h, i) => {
          safeText(h, hx, startY + 7, { width: colWidths[i] - 5 });
          hx += colWidths[i];
        });
      };

      // ==================== PAGINA 1: COPERTINA E KPI ====================

      // Banner header
      doc.rect(0, 0, doc.page.width, 80).fill(PRIMARY_COLOR);
      doc.fillColor('#FFFFFF').fontSize(28).font('Helvetica-Bold');
      safeText('REPORT CONTROLLO QUALITÀ', 40, 22);
      doc.fontSize(12).font('Helvetica');
      safeText('CoreGRE Quality Control System', 40, 54);

      doc.fillColor(SECONDARY).fontSize(10);
      safeText(`Generato: ${new Date().toLocaleString('it-IT')}`, 40, 100);

      // Box filtri
      const filterBoxY = 130;
      doc.rect(40, filterBoxY, 290, 95).stroke(PRIMARY_COLOR);
      doc.fillColor(PRIMARY_COLOR).fontSize(11).font('Helvetica-Bold');
      safeText('FILTRI APPLICATI', 50, filterBoxY + 10);

      doc.fillColor(SECONDARY).fontSize(9).font('Helvetica');
      let fy = filterBoxY + 30;
      if (filters.dataInizio || filters.dataFine) {
        safeText(`Periodo: ${filters.dataInizio || 'inizio'} → ${filters.dataFine || 'oggi'}`, 50, fy);
        fy += 15;
      }
      if (filters.reparto)   { safeText(`Reparto: ${departmentMap.get(filters.reparto) || filters.reparto}`, 50, fy); fy += 15; }
      if (filters.operatore) { safeText(`Operatore: ${filters.operatore}`, 50, fy); fy += 15; }
      if (filters.tipoCq)    { safeText(`Tipo CQ: ${filters.tipoCq}`, 50, fy); fy += 15; }
      if (fy === filterBoxY + 30) { doc.fillColor('#888'); safeText('Nessun filtro applicato', 50, fy); doc.fillColor(SECONDARY); }

      // KPI boxes
      const kpiX = 350;
      const kpiY = filterBoxY;
      const kpiW = 460;
      doc.rect(kpiX, kpiY, kpiW, 95).fill(ACCENT_COLOR).stroke(PRIMARY_COLOR);
      doc.fillColor(PRIMARY_COLOR).fontSize(11).font('Helvetica-Bold');
      safeText('INDICATORI CHIAVE', kpiX + 10, kpiY + 10);

      const kpis = [
        { label: 'Controlli Totali',   value: totalRecords.toLocaleString('it-IT'),  x: kpiX + 15,  y: kpiY + 32 },
        { label: 'Con Eccezioni',      value: withExceptions.toLocaleString('it-IT'), x: kpiX + 125, y: kpiY + 32 },
        { label: 'Conformi (OK)',       value: ok.toLocaleString('it-IT'),            x: kpiX + 255, y: kpiY + 32 },
        { label: 'Tasso Conformità',   value: `${successRate.toFixed(1)}%`,           x: kpiX + 365, y: kpiY + 32 },
      ];

      kpis.forEach((k) => {
        doc.fillColor('#666').fontSize(8).font('Helvetica');
        safeText(k.label, k.x, k.y);
        // colore valore
        if (k.label === 'Con Eccezioni') doc.fillColor('#C62828');
        else if (k.label === 'Conformi (OK)') doc.fillColor('#2E7D32');
        else if (k.label === 'Tasso Conformità') doc.fillColor(successRate >= 90 ? '#2E7D32' : successRate >= 70 ? '#E65100' : '#C62828');
        else doc.fillColor(SECONDARY);
        doc.fontSize(16).font('Helvetica-Bold');
        safeText(k.value, k.x, k.y + 13);
        doc.fillColor(SECONDARY);
      });

      // Barra conformità visiva
      const barY = kpiY + 70;
      const barTotalW = kpiW - 20;
      doc.rect(kpiX + 10, barY, barTotalW, 12).fill('#E0E0E0');
      const conformityColor = successRate >= 90 ? '#2E7D32' : successRate >= 70 ? '#F57C00' : '#C62828';
      doc.rect(kpiX + 10, barY, (successRate / 100) * barTotalW, 12).fill(conformityColor);
      doc.fillColor('#fff').fontSize(7).font('Helvetica-Bold');
      safeText(`${successRate.toFixed(1)}% conformità`, kpiX + 15, barY + 2);

      // ---- Sezione tipologie difetti (riepilogo grafico) ----
      const excEntries = Object.entries(excTypes).sort((a, b) => b[1] - a[1]).slice(0, 8);
      if (excEntries.length > 0) {
        const excSectionY = 250;
        doc.fillColor(PRIMARY_COLOR).fontSize(12).font('Helvetica-Bold');
        safeText('TOP TIPOLOGIE DIFETTI', 40, excSectionY);

        const maxCount = excEntries[0][1];
        const excBarMaxW = pageWidth - 250;
        let excY = excSectionY + 20;

        excEntries.forEach(([type, count], i) => {
          if (i % 2 === 0) doc.rect(40, excY - 2, pageWidth, 18).fill('#F5F5F5');
          doc.fillColor(SECONDARY).fontSize(8).font('Helvetica');
          safeText(type.substring(0, 35), 45, excY + 3, { width: 220 });

          const barW = maxCount > 0 ? (count / maxCount) * excBarMaxW : 0;
          doc.rect(270, excY + 1, barW, 12).fill(PRIMARY_COLOR);
          doc.fillColor('#555').fontSize(7).font('Helvetica-Bold');
          safeText(count.toString(), 270 + barW + 5, excY + 3);

          excY += 18;
        });

        const totalExc = Object.values(excTypes).reduce((a, b) => a + b, 0);
        excY += 5;
        doc.rect(40, excY, pageWidth, 20).fill(PRIMARY_COLOR);
        doc.fillColor('#fff').fontSize(9).font('Helvetica-Bold');
        safeText('TOTALE ECCEZIONI', 45, excY + 5);
        safeText(totalExc.toLocaleString('it-IT'), 270, excY + 5);
      }

      // ==================== PAGINA 2: ANALISI PER REPARTO ====================
      if (Object.keys(byDept).length > 0) {
        doc.addPage();

        doc.rect(0, 0, doc.page.width, 50).fill(PRIMARY_COLOR);
        doc.fillColor('#FFFFFF').fontSize(18).font('Helvetica-Bold');
        safeText('ANALISI PER REPARTO', 40, 15);
        doc.fontSize(9).font('Helvetica');
        safeText(`${totalRecords} controlli elaborati`, 40, 37);

        const headers = ['Reparto', 'Totale Controlli', 'Conformi (OK)', 'Con Eccezioni', '% Conformità', 'Andamento'];
        const colW    = [200, 90, 90, 90, 80, 200];
        const totalW  = colW.reduce((a, b) => a + b, 0);

        let tableY = 70;
        drawTableHeader(headers, colW, tableY, totalW);
        tableY += 22;

        const deptKeys = Object.keys(byDept).sort();
        let rowIdx = 0;

        deptKeys.forEach((dept) => {
          if (needsNewPage(tableY, 20)) {
            doc.addPage();
            tableY = 50;
            drawTableHeader(headers, colW, tableY, totalW);
            tableY += 22;
          }

          const stats = byDept[dept];
          const rate = stats.total > 0 ? (stats.ok / stats.total) * 100 : 0;

          if (rowIdx % 2 === 0) doc.rect(40, tableY, totalW, 20).fill('#F8F9FA');
          doc.fillColor(SECONDARY).fontSize(8).font('Helvetica');

          let ax = 45;
          safeText(dept.substring(0, 28), ax, tableY + 6, { width: colW[0] - 5 });
          ax += colW[0];
          safeText(stats.total.toString(), ax, tableY + 6);
          ax += colW[1];
          doc.fillColor('#2E7D32');
          safeText(stats.ok.toString(), ax, tableY + 6);
          ax += colW[2];
          doc.fillColor('#C62828');
          safeText(stats.exceptions.toString(), ax, tableY + 6);
          ax += colW[3];
          const rateColor = rate >= 90 ? '#2E7D32' : rate >= 70 ? '#E65100' : '#C62828';
          doc.fillColor(rateColor).font('Helvetica-Bold');
          safeText(`${rate.toFixed(1)}%`, ax, tableY + 6);
          ax += colW[4];

          // Barra grafica conformità
          const barW = (rate / 100) * (colW[5] - 35);
          doc.rect(ax, tableY + 5, barW, 10).fill(rateColor);
          doc.fillColor(SECONDARY).font('Helvetica');
          safeText(`${rate.toFixed(1)}%`, ax + barW + 3, tableY + 6);

          tableY += 20;
          rowIdx++;
          doc.fillColor(SECONDARY);
        });

        // Totale riga
        doc.rect(40, tableY, totalW, 22).fill(PRIMARY_COLOR);
        doc.fillColor('#FFFFFF').fontSize(9).font('Helvetica-Bold');
        safeText('TOTALE', 45, tableY + 6);
        safeText(totalRecords.toString(), 45 + colW[0], tableY + 6);
        doc.fillColor('#A5D6A7');
        safeText(ok.toString(), 45 + colW[0] + colW[1], tableY + 6);
        doc.fillColor('#EF9A9A');
        safeText(withExceptions.toString(), 45 + colW[0] + colW[1] + colW[2], tableY + 6);
        doc.fillColor('#FFFFFF');
        safeText(`${successRate.toFixed(1)}%`, 45 + colW[0] + colW[1] + colW[2] + colW[3], tableY + 6);
      }

      // ==================== PAGINA 3: ANALISI PER OPERATORE ====================
      if (Object.keys(byOp).length > 0) {
        doc.addPage();

        doc.rect(0, 0, doc.page.width, 50).fill(PRIMARY_COLOR);
        doc.fillColor('#FFFFFF').fontSize(18).font('Helvetica-Bold');
        safeText('ANALISI PER OPERATORE', 40, 15);

        const headers = ['Operatore', 'Totale Controlli', 'Conformi (OK)', 'Con Eccezioni', '% Conformità', 'Andamento'];
        const colW    = [200, 90, 90, 90, 80, 200];
        const totalW  = colW.reduce((a, b) => a + b, 0);

        let tableY = 70;
        drawTableHeader(headers, colW, tableY, totalW);
        tableY += 22;

        const opKeys = Object.keys(byOp).sort();
        let rowIdx = 0;

        opKeys.forEach((op) => {
          if (needsNewPage(tableY, 20)) {
            doc.addPage();
            tableY = 50;
            drawTableHeader(headers, colW, tableY, totalW);
            tableY += 22;
          }

          const stats = byOp[op];
          const rate = stats.total > 0 ? (stats.ok / stats.total) * 100 : 0;

          if (rowIdx % 2 === 0) doc.rect(40, tableY, totalW, 20).fill('#F8F9FA');
          doc.fillColor(SECONDARY).fontSize(8).font('Helvetica');

          let ax = 45;
          safeText(op.substring(0, 28), ax, tableY + 6, { width: colW[0] - 5 });
          ax += colW[0];
          safeText(stats.total.toString(), ax, tableY + 6);
          ax += colW[1];
          doc.fillColor('#2E7D32');
          safeText(stats.ok.toString(), ax, tableY + 6);
          ax += colW[2];
          doc.fillColor('#C62828');
          safeText(stats.exceptions.toString(), ax, tableY + 6);
          ax += colW[3];
          const rateColor = rate >= 90 ? '#2E7D32' : rate >= 70 ? '#E65100' : '#C62828';
          doc.fillColor(rateColor).font('Helvetica-Bold');
          safeText(`${rate.toFixed(1)}%`, ax, tableY + 6);
          ax += colW[4];

          const barW = (rate / 100) * (colW[5] - 35);
          doc.rect(ax, tableY + 5, barW, 10).fill(rateColor);
          doc.fillColor(SECONDARY).font('Helvetica');
          safeText(`${rate.toFixed(1)}%`, ax + barW + 3, tableY + 6);

          tableY += 20;
          rowIdx++;
          doc.fillColor(SECONDARY);
        });

        // Totale riga
        doc.rect(40, tableY, totalW, 22).fill(PRIMARY_COLOR);
        doc.fillColor('#FFFFFF').fontSize(9).font('Helvetica-Bold');
        safeText('TOTALE', 45, tableY + 6);
        safeText(totalRecords.toString(), 45 + colW[0], tableY + 6);
        doc.fillColor('#A5D6A7');
        safeText(ok.toString(), 45 + colW[0] + colW[1], tableY + 6);
        doc.fillColor('#EF9A9A');
        safeText(withExceptions.toString(), 45 + colW[0] + colW[1] + colW[2], tableY + 6);
        doc.fillColor('#FFFFFF');
        safeText(`${successRate.toFixed(1)}%`, 45 + colW[0] + colW[1] + colW[2] + colW[3], tableY + 6);
      }

      // ==================== PAGINA 4: TUTTE LE TIPOLOGIE DIFETTI ====================
      if (Object.keys(excTypes).length > 0) {
        doc.addPage();

        doc.rect(0, 0, doc.page.width, 50).fill(PRIMARY_COLOR);
        doc.fillColor('#FFFFFF').fontSize(18).font('Helvetica-Bold');
        safeText('TIPOLOGIE DIFETTI — DETTAGLIO COMPLETO', 40, 15);

        const headers = ['Tipo Difetto', 'Occorrenze', '% sul Totale Eccezioni', 'Distribuzione'];
        const colW    = [280, 70, 130, 270];
        const totalW  = colW.reduce((a, b) => a + b, 0);

        let tableY = 70;
        drawTableHeader(headers, colW, tableY, totalW);
        tableY += 22;

        const totalExc = Object.values(excTypes).reduce((a, b) => a + b, 0);
        const excSorted = Object.entries(excTypes).sort((a, b) => b[1] - a[1]);
        let rowIdx = 0;

        excSorted.forEach(([type, count]) => {
          if (needsNewPage(tableY, 20)) {
            doc.addPage();
            tableY = 50;
            drawTableHeader(headers, colW, tableY, totalW);
            tableY += 22;
          }

          const perc = totalExc > 0 ? (count / totalExc) * 100 : 0;

          if (rowIdx % 2 === 0) doc.rect(40, tableY, totalW, 20).fill('#F8F9FA');
          doc.fillColor(SECONDARY).fontSize(8).font('Helvetica');

          let ax = 45;
          safeText(type.substring(0, 40), ax, tableY + 6, { width: colW[0] - 5 });
          ax += colW[0];
          doc.font('Helvetica-Bold');
          safeText(count.toString(), ax, tableY + 6);
          ax += colW[1];
          doc.font('Helvetica');
          safeText(`${perc.toFixed(1)}%`, ax, tableY + 6);
          ax += colW[2];

          // Barra grafica
          const barW = (perc / 100) * (colW[3] - 30);
          doc.rect(ax, tableY + 5, barW, 10).fill(PRIMARY_COLOR);

          tableY += 20;
          rowIdx++;
          doc.fillColor(SECONDARY);
        });

        // Totale
        doc.rect(40, tableY, totalW, 22).fill(PRIMARY_COLOR);
        doc.fillColor('#FFFFFF').fontSize(9).font('Helvetica-Bold');
        safeText('TOTALE ECCEZIONI', 45, tableY + 6);
        safeText(totalExc.toLocaleString('it-IT'), 45 + colW[0], tableY + 6);
        safeText('100%', 45 + colW[0] + colW[1], tableY + 6);
      }

      // ==================== FOOTER SU TUTTE LE PAGINE ====================
      const range = doc.bufferedPageRange();
      for (let i = 0; i < range.count; i++) {
        doc.switchToPage(i);
        doc.fontSize(7).font('Helvetica').fillColor('#999');
        safeText(
          `Pagina ${i + 1} di ${range.count}  •  CoreGRE Quality Control System  •  ${new Date().toLocaleDateString('it-IT')}`,
          0, pageHeight - 30,
          { width: doc.page.width, align: 'center' }
        );
        // Linea separatrice footer
        doc.strokeColor('#DDD').lineWidth(0.5)
           .moveTo(40, pageHeight - 35)
           .lineTo(doc.page.width - 40, pageHeight - 35)
           .stroke();
      }

      doc.end();
    } catch (error) {
      reject(error);
    }
  });
}

export default handler;
