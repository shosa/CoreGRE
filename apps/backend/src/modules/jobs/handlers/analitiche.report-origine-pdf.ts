import PDFDocument = require('pdfkit');
import { JobHandler } from '../types';
import * as fs from 'fs';

interface ReportOrigineFilters {
  userId: number;
  jobId: string;
  dataFrom?: string;
  dataTo?: string;
  repartoOrigineId?: number;
  tipoDocumento?: string;
  linea?: string;
  groupBy?: 'reparto' | 'linea' | 'tipoDocumento' | 'mese';
  includeArticoliPerReparto?: boolean;
  showUncorrelatedCosts?: boolean;
}

const COST_FIELDS = ['costoTaglio', 'costoOrlatura', 'costoStrobel', 'altriCosti', 'costoMontaggio'];
const COST_LABELS: Record<string, string> = {
  costoTaglio: 'Taglio',
  costoOrlatura: 'Orlatura',
  costoStrobel: 'Strobel',
  altriCosti: 'Altri',
  costoMontaggio: 'Montaggio',
};

/**
 * Calcola i costi applicabili per un record basandosi sul REPARTO DI ORIGINE.
 * I costiAssociati vengono letti dal reparto origine (repartoId), non dal finale.
 */
function getApplicableCosts(
  record: any,
  repartoMap: Map<number, any>,
  showUncorrelatedCosts: boolean = false,
): { costs: Record<string, number>; totalCosto: number; fatturato: number } {
  const qty = Number(record.quantita) || 0;
  const prezzoUnit = Number(record.prezzoUnitario) || 0;
  const fatturato = qty * prezzoUnit;

  // Usa i costiAssociati del reparto ORIGINE
  const repartoOrigine = record.repartoId ? repartoMap.get(record.repartoId) : null;
  let costiAssociati: string[] | null = null;

  if (!showUncorrelatedCosts && repartoOrigine?.costiAssociati) {
    try {
      costiAssociati = typeof repartoOrigine.costiAssociati === 'string'
        ? JSON.parse(repartoOrigine.costiAssociati)
        : repartoOrigine.costiAssociati;
    } catch {
      costiAssociati = null;
    }
  }

  const costs: Record<string, number> = {};
  let totalCosto = 0;

  COST_FIELDS.forEach((field) => {
    let costoUnit = Number(record[field]) || 0;

    if (!showUncorrelatedCosts && costiAssociati && costiAssociati.length > 0 && !costiAssociati.includes(field)) {
      costoUnit = 0;
    }

    const costoTotale = costoUnit * qty;
    costs[field] = costoTotale;
    totalCosto += costoTotale;
  });

  return { costs, totalCosto, fatturato };
}

function formatCurrency(value: number): string {
  if (value === 0) return '-';
  return `€ ${value.toLocaleString('it-IT', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
}

function groupRecordsByKey(
  records: any[],
  groupBy: string,
  repartoMap: Map<number, any>,
  showUncorrelatedCosts: boolean,
): Map<string, any> {
  const grouped = new Map<string, any>();

  records.forEach((r: any) => {
    let key: string;
    switch (groupBy) {
      case 'reparto':
        key = r.reparto?.nome || 'Non assegnato';
        break;
      case 'linea':
        key = r.linea || 'Non specificata';
        break;
      case 'tipoDocumento':
        key = r.tipoDocumento || 'Non specificato';
        break;
      case 'mese':
        key = r.dataDocumento ? new Date(r.dataDocumento).toISOString().slice(0, 7) : 'Senza data';
        break;
      default:
        key = 'Altro';
    }

    if (!grouped.has(key)) {
      grouped.set(key, { count: 0, quantita: 0, costoTaglio: 0, costoOrlatura: 0, costoStrobel: 0, altriCosti: 0, costoMontaggio: 0, totalCosto: 0, fatturato: 0 });
    }

    const g = grouped.get(key);
    const { costs, totalCosto, fatturato } = getApplicableCosts(r, repartoMap, showUncorrelatedCosts);
    g.count++;
    g.quantita += Number(r.quantita) || 0;
    COST_FIELDS.forEach(f => { g[f] += costs[f]; });
    g.totalCosto += totalCosto;
    g.fatturato += fatturato;
  });

  return grouped;
}

const handler: JobHandler = async (payload, helpers) => {
  const {
    userId,
    jobId,
    dataFrom,
    dataTo,
    repartoOrigineId,
    tipoDocumento,
    linea,
    groupBy = 'reparto',
    includeArticoliPerReparto = false,
    showUncorrelatedCosts = false,
  } = payload as ReportOrigineFilters;

  const { ensureOutputPath, trackingService } = helpers;
  const prisma = (trackingService as any).prisma;

  const dateStr = new Date().toISOString().split('T')[0];
  const fileName = `REPORT_ORIGINE_${groupBy.toUpperCase()}_${dateStr}.pdf`;
  const { fullPath } = await ensureOutputPath(userId, jobId, fileName);

  // Filtra su repartoId (origine) — solo prodotti Italia (prodottoEstero = false)
  const where: any = {
    prodottoEstero: false,
    repartoId: { not: null },
  };

  if (dataFrom || dataTo) {
    where.dataDocumento = {};
    if (dataFrom) where.dataDocumento.gte = new Date(dataFrom);
    if (dataTo) {
      const endDate = new Date(dataTo);
      endDate.setHours(23, 59, 59, 999);
      where.dataDocumento.lte = endDate;
    }
  }
  if (repartoOrigineId) where.repartoId = repartoOrigineId;
  if (tipoDocumento) where.tipoDocumento = tipoDocumento;
  if (linea) where.linea = linea;

  const records = await prisma.analiticaRecord.findMany({
    where,
    include: { reparto: true, repartoFinale: true },
    orderBy: { dataDocumento: 'desc' },
  });

  const totalCount = await prisma.analiticaRecord.count({
    where: {
      prodottoEstero: false,
      ...(dataFrom || dataTo ? { dataDocumento: { ...(dataFrom ? { gte: new Date(dataFrom) } : {}), ...(dataTo ? { lte: new Date(dataTo) } : {}) } } : {}),
      ...(tipoDocumento ? { tipoDocumento } : {}),
      ...(linea ? { linea } : {}),
    },
  });
  const excludedCount = totalCount - records.length;

  const reparti = await prisma.analiticaReparto.findMany({ orderBy: { ordine: 'asc' } });
  const repartoMap: Map<number, any> = new Map(reparti.map((r: any) => [r.id as number, r]));

  const buffer = await generatePdf(records, repartoMap, {
    dataFrom, dataTo, repartoOrigineId, tipoDocumento, linea, groupBy,
    includeArticoliPerReparto, showUncorrelatedCosts, excludedCount,
  });

  fs.writeFileSync(fullPath, buffer);
  const stat = fs.statSync(fullPath);

  return {
    outputPath: fullPath,
    outputName: fileName,
    outputMime: 'application/pdf',
    outputSize: Number(stat.size),
  };
};

async function generatePdf(records: any[], repartoMap: Map<number, any>, filters: any): Promise<Buffer> {
  return new Promise((resolve, reject) => {
    try {
      const doc = new PDFDocument({ margin: 40, size: 'A4', layout: 'landscape', autoFirstPage: true });
      const chunks: Buffer[] = [];
      doc.on('data', (c) => chunks.push(c));
      doc.on('end', () => resolve(Buffer.concat(chunks)));
      doc.on('error', reject);

      const pageWidth = doc.page.width - 80;
      const pageHeight = doc.page.height;
      const usableBottom = pageHeight - 40;
      const primaryColor = '#1B5E20'; // Verde scuro per distinguerlo dal report finale
      const secondaryColor = '#424242';
      const accentColor = '#E8F5E9';

      const safeText = (text: string, x: number, y: number, opts?: any) => {
        doc.text(text, x, y, { lineBreak: false, ...opts });
      };
      const needsNewPage = (y: number, h: number) => (y + h) > usableBottom;
      const drawHeader = (headers: string[], colWidths: number[], totalW: number, startY: number) => {
        doc.rect(40, startY, totalW, 22).fill(primaryColor);
        doc.fillColor('#FFFFFF').fontSize(8).font('Helvetica-Bold');
        let hx = 45;
        headers.forEach((h, i) => { safeText(h, hx, startY + 7, { width: colWidths[i] - 5 }); hx += colWidths[i]; });
      };

      // ==================== COPERTINA ====================
      doc.rect(0, 0, doc.page.width, 80).fill(primaryColor);
      doc.fillColor('#FFFFFF').fontSize(28).font('Helvetica-Bold');
      safeText('REPORT ANALISI COSTI - REPARTO ORIGINE', 40, 20);
      doc.fontSize(12).font('Helvetica');
      safeText('Analisi basata sul reparto di origine (prodotti Italia)', 40, 55);

      doc.fillColor(secondaryColor).fontSize(10);
      safeText(`Generato: ${new Date().toLocaleString('it-IT')}`, 40, 100);

      // Box filtri
      const filterBoxY = 130;
      doc.rect(40, filterBoxY, 300, 110).stroke(primaryColor);
      doc.fillColor(primaryColor).fontSize(11).font('Helvetica-Bold');
      safeText('FILTRI APPLICATI', 50, filterBoxY + 10);
      doc.fillColor(secondaryColor).fontSize(9).font('Helvetica');
      let filterY = filterBoxY + 30;

      if (filters.dataFrom || filters.dataTo) {
        safeText(`Periodo: ${filters.dataFrom || 'inizio'} → ${filters.dataTo || 'oggi'}`, 50, filterY);
        filterY += 15;
      }
      if (filters.repartoOrigineId) {
        const rep = repartoMap.get(filters.repartoOrigineId);
        safeText(`Reparto Origine: ${rep?.nome || filters.repartoOrigineId}`, 50, filterY);
        filterY += 15;
      }
      if (filters.tipoDocumento) {
        safeText(`Tipo Documento: ${filters.tipoDocumento}`, 50, filterY);
        filterY += 15;
      }
      if (filters.linea) {
        safeText(`Linea: ${filters.linea}`, 50, filterY);
        filterY += 15;
      }
      safeText(`Raggruppamento: ${filters.groupBy.toUpperCase()}`, 50, filterY);
      filterY += 15;
      doc.fillColor('#1B5E20').font('Helvetica-Bold');
      safeText('Solo prodotti ITALIA (prodottoEstero = false)', 50, filterY);
      doc.fillColor(secondaryColor).font('Helvetica');

      // Calcola totali
      let totalQuantita = 0, totalFatturato = 0, grandTotalCosti = 0;
      const totalCosts: Record<string, number> = {};
      COST_FIELDS.forEach(f => totalCosts[f] = 0);

      records.forEach((r: any) => {
        const { costs, totalCosto, fatturato } = getApplicableCosts(r, repartoMap, filters.showUncorrelatedCosts);
        totalQuantita += Number(r.quantita) || 0;
        totalFatturato += fatturato;
        grandTotalCosti += totalCosto;
        COST_FIELDS.forEach(f => totalCosts[f] += costs[f]);
      });

      // KPI box
      const kpiBoxX = 360, kpiBoxW = 420;
      doc.rect(kpiBoxX, filterBoxY, kpiBoxW, 110).fill(accentColor).stroke(primaryColor);
      doc.fillColor(primaryColor).fontSize(11).font('Helvetica-Bold');
      safeText('INDICATORI CHIAVE', kpiBoxX + 10, filterBoxY + 10);

      const kpiData = [
        { label: 'Record Analizzati', value: records.length.toLocaleString('it-IT'), x: kpiBoxX + 15, y: filterBoxY + 35 },
        { label: 'Quantità Totale', value: totalQuantita.toLocaleString('it-IT', { maximumFractionDigits: 0 }), x: kpiBoxX + 115, y: filterBoxY + 35 },
        { label: 'Totale Costi', value: `€ ${grandTotalCosti.toLocaleString('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`, x: kpiBoxX + 215, y: filterBoxY + 35 },
        { label: 'Fatturato', value: `€ ${totalFatturato.toLocaleString('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`, x: kpiBoxX + 315, y: filterBoxY + 35 },
      ];
      kpiData.forEach(kpi => {
        doc.fillColor('#666').fontSize(8).font('Helvetica');
        safeText(kpi.label, kpi.x, kpi.y);
        doc.fillColor(secondaryColor).fontSize(14).font('Helvetica-Bold');
        safeText(kpi.value, kpi.x, kpi.y + 12);
      });

      if (filters.excludedCount > 0) {
        doc.fillColor('#D32F2F').fontSize(9).font('Helvetica-Oblique');
        safeText(`** ${filters.excludedCount} record Italia esclusi (reparto origine non assegnato).`, 40, filterBoxY + 125, { width: pageWidth });
      }

      // Tabella composizione costi
      const costTableY = 270;
      doc.fillColor(primaryColor).fontSize(12).font('Helvetica-Bold');
      safeText('DETTAGLIO COMPOSIZIONE COSTI', 40, costTableY);

      const costHeaders = ['Voce di Costo', 'Importo (€)', 'Incidenza'];
      const costColWidths = [200, 150, 350];
      const costTotalW = costColWidths.reduce((a, b) => a + b, 0);

      let tableY = costTableY + 20;
      doc.rect(40, tableY, costTotalW, 22).fill(primaryColor);
      doc.fillColor('#FFFFFF').fontSize(9).font('Helvetica-Bold');
      let tableX = 45;
      costHeaders.forEach((h, i) => { safeText(h, tableX, tableY + 7, { width: costColWidths[i] - 10 }); tableX += costColWidths[i]; });
      tableY += 22;
      doc.font('Helvetica').fontSize(9);

      COST_FIELDS.forEach((field, idx) => {
        const value = totalCosts[field];
        const perc = grandTotalCosti > 0 ? (value / grandTotalCosti) * 100 : 0;
        if (idx % 2 === 0) doc.rect(40, tableY, costTotalW, 20).fill('#F5F5F5');
        doc.fillColor(secondaryColor);
        tableX = 45;
        safeText(COST_LABELS[field], tableX, tableY + 6);
        tableX += costColWidths[0];
        safeText(`€ ${value.toLocaleString('it-IT', { minimumFractionDigits: 2 })}`, tableX, tableY + 6);
        tableX += costColWidths[1];
        const barWidth = (perc / 100) * 320;
        doc.rect(tableX, tableY + 5, barWidth, 10).fill(primaryColor);
        doc.fillColor(secondaryColor);
        safeText(`${perc.toFixed(1)}%`, tableX + barWidth + 5, tableY + 6);
        tableY += 20;
      });

      doc.rect(40, tableY, costTotalW, 22).fill(primaryColor);
      doc.fillColor('#FFFFFF').fontSize(10).font('Helvetica-Bold');
      safeText('TOTALE COSTI', 45, tableY + 6);
      safeText(`€ ${grandTotalCosti.toLocaleString('it-IT', { minimumFractionDigits: 2 })}`, 45 + costColWidths[0], tableY + 6);
      tableY += 22;
      doc.rect(40, tableY, costTotalW, 22).fill('#E8F5E9');
      doc.fillColor('#2E7D32').fontSize(10).font('Helvetica-Bold');
      safeText('FATTURATO', 45, tableY + 6);
      safeText(`€ ${totalFatturato.toLocaleString('it-IT', { minimumFractionDigits: 2 })}`, 45 + costColWidths[0], tableY + 6);

      // ==================== PAGINA 2: ANALISI PER GRUPPO ====================
      doc.addPage();
      doc.rect(0, 0, doc.page.width, 50).fill(primaryColor);
      doc.fillColor('#FFFFFF').fontSize(18).font('Helvetica-Bold');
      safeText(`ANALISI PER ${filters.groupBy.toUpperCase()} (REPARTO ORIGINE)`, 40, 15);

      const grouped = groupRecordsByKey(records, filters.groupBy, repartoMap, filters.showUncorrelatedCosts);
      const analysisHeaders = [filters.groupBy.charAt(0).toUpperCase() + filters.groupBy.slice(1), 'N° Doc', 'Quantità', 'Taglio', 'Orlatura', 'Strobel', 'Altri', 'Montaggio', 'Tot. Costi', 'Fatturato'];
      const analysisColWidths = [110, 45, 65, 65, 65, 65, 65, 70, 85, 85];
      const totalTableWidth = analysisColWidths.reduce((a, b) => a + b, 0);

      let analysisY = 70;
      drawHeader(analysisHeaders, analysisColWidths, totalTableWidth, analysisY);
      analysisY += 22;
      doc.font('Helvetica').fontSize(8);

      let rowIdx = 0;
      Array.from(grouped.keys()).sort().forEach((key) => {
        if (needsNewPage(analysisY, 18)) {
          doc.addPage();
          analysisY = 50;
          drawHeader(analysisHeaders, analysisColWidths, totalTableWidth, analysisY);
          analysisY += 22;
          doc.font('Helvetica').fontSize(8);
        }
        const g = grouped.get(key);
        if (rowIdx % 2 === 0) doc.rect(40, analysisY, totalTableWidth, 18).fill('#F8F9FA');
        doc.fillColor(secondaryColor);
        let ax = 45;
        safeText(key.substring(0, 16), ax, analysisY + 5, { width: analysisColWidths[0] - 5 });
        ax += analysisColWidths[0];
        safeText(g.count.toString(), ax, analysisY + 5); ax += analysisColWidths[1];
        safeText(g.quantita.toLocaleString('it-IT', { maximumFractionDigits: 0 }), ax, analysisY + 5); ax += analysisColWidths[2];
        safeText(formatCurrency(g.costoTaglio), ax, analysisY + 5); ax += analysisColWidths[3];
        safeText(formatCurrency(g.costoOrlatura), ax, analysisY + 5); ax += analysisColWidths[4];
        safeText(formatCurrency(g.costoStrobel), ax, analysisY + 5); ax += analysisColWidths[5];
        safeText(formatCurrency(g.altriCosti), ax, analysisY + 5); ax += analysisColWidths[6];
        safeText(formatCurrency(g.costoMontaggio), ax, analysisY + 5); ax += analysisColWidths[7];
        doc.font('Helvetica-Bold');
        safeText(formatCurrency(g.totalCosto), ax, analysisY + 5); ax += analysisColWidths[8];
        doc.fillColor('#2E7D32');
        safeText(formatCurrency(g.fatturato), ax, analysisY + 5);
        analysisY += 18;
        rowIdx++;
        doc.font('Helvetica').fillColor(secondaryColor);
      });

      // ==================== PAGINA 3: DETTAGLIO ARTICOLI (opzionale) ====================
      if (filters.includeArticoliPerReparto) {
        doc.addPage();
        doc.rect(0, 0, doc.page.width, 50).fill(primaryColor);
        doc.fillColor('#FFFFFF').fontSize(18).font('Helvetica-Bold');
        safeText('DETTAGLIO ARTICOLI PER REPARTO ORIGINE E LINEA', 40, 15);

        const byRepartoLinea = new Map<string, Map<string, Map<string, any>>>();
        records.forEach((r: any) => {
          const repartoNome = r.reparto?.nome || 'Non assegnato';
          const lineaNome = r.linea || 'Non specificata';
          const articoloNome = r.articolo || 'N/D';
          if (!byRepartoLinea.has(repartoNome)) byRepartoLinea.set(repartoNome, new Map());
          const lineaMap = byRepartoLinea.get(repartoNome)!;
          if (!lineaMap.has(lineaNome)) lineaMap.set(lineaNome, new Map());
          const articoloMap = lineaMap.get(lineaNome)!;
          if (!articoloMap.has(articoloNome)) articoloMap.set(articoloNome, { quantita: 0, costo: 0, fatturato: 0, descrizione: r.descrizioneArt || '' });
          const art = articoloMap.get(articoloNome)!;
          const { totalCosto, fatturato } = getApplicableCosts(r, repartoMap, filters.showUncorrelatedCosts);
          art.quantita += Number(r.quantita) || 0;
          art.costo += totalCosto;
          art.fatturato += fatturato;
        });

        let detailY = 70;
        Array.from(byRepartoLinea.keys()).sort().forEach((repartoNome) => {
          if (needsNewPage(detailY, 58)) { doc.addPage(); detailY = 50; }
          doc.rect(40, detailY, pageWidth, 20).fill(primaryColor);
          doc.fillColor('#FFFFFF').fontSize(10).font('Helvetica-Bold');
          safeText(`REPARTO ORIGINE: ${repartoNome}`, 45, detailY + 5);
          detailY += 25;

          byRepartoLinea.get(repartoNome)!.forEach((articoloMap, lineaNome) => {
            if (needsNewPage(detailY, 38)) { doc.addPage(); detailY = 50; }
            doc.rect(50, detailY, pageWidth - 20, 16).fill(accentColor);
            doc.fillColor(primaryColor).fontSize(9).font('Helvetica-Bold');
            safeText(`Linea: ${lineaNome}`, 55, detailY + 4);
            detailY += 20;
            doc.fillColor('#666').fontSize(7).font('Helvetica-Bold');
            safeText('Articolo', 60, detailY); safeText('Descrizione', 180, detailY);
            safeText('Quantità', 380, detailY); safeText('Costo Tot.', 450, detailY); safeText('Fatturato', 530, detailY);
            detailY += 12;
            doc.font('Helvetica').fontSize(7).fillColor(secondaryColor);
            Array.from(articoloMap.keys()).sort().slice(0, 15).forEach((artNome) => {
              if (needsNewPage(detailY, 10)) {
                doc.addPage(); detailY = 50;
                doc.fillColor('#666').fontSize(7).font('Helvetica-Bold');
                safeText('Articolo', 60, detailY); safeText('Descrizione', 180, detailY);
                safeText('Quantità', 380, detailY); safeText('Costo Tot.', 450, detailY); safeText('Fatturato', 530, detailY);
                detailY += 12;
                doc.font('Helvetica').fontSize(7).fillColor(secondaryColor);
              }
              const art = articoloMap.get(artNome)!;
              safeText(artNome.substring(0, 20), 60, detailY);
              safeText((art.descrizione || '').substring(0, 30), 180, detailY);
              safeText(art.quantita.toLocaleString('it-IT', { maximumFractionDigits: 0 }), 380, detailY);
              safeText(`€ ${art.costo.toLocaleString('it-IT', { minimumFractionDigits: 2 })}`, 450, detailY);
              doc.fillColor('#2E7D32');
              safeText(`€ ${art.fatturato.toLocaleString('it-IT', { minimumFractionDigits: 2 })}`, 530, detailY);
              doc.fillColor(secondaryColor);
              detailY += 10;
            });
            const keys = Array.from(articoloMap.keys());
            if (keys.length > 15) {
              doc.fillColor('#999').fontSize(7).font('Helvetica-Oblique');
              safeText(`... e altri ${keys.length - 15} articoli`, 60, detailY);
              detailY += 10;
            }
            detailY += 8;
          });
          detailY += 10;
        });
      }

      doc.end();
    } catch (error) {
      reject(error);
    }
  });
}

export default handler;
