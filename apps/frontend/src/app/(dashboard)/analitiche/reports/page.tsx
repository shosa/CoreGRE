"use client";

import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import { analiticheApi } from "@/lib/api";
import { showError, showSuccess } from "@/store/notifications";
import PageHeader from "@/components/layout/PageHeader";
import Breadcrumb from "@/components/layout/Breadcrumb";

const containerVariants = {
  hidden: { opacity: 0 },
  visible: { opacity: 1, transition: { staggerChildren: 0.1 } },
};

const itemVariants = {
  hidden: { opacity: 0, y: 20 },
  visible: { opacity: 1, y: 0 },
};

interface Reparto {
  id: number;
  nome: string;
  codice: string | null;
  attivo: boolean;
}

interface Filters {
  linee: string[];
  tipiDocumento: string[];
}

export default function AnaliticheReportsPage() {
  const [loading, setLoading] = useState(true);
  const [generating, setGenerating] = useState(false);
  const [generatingProduzione, setGeneratingProduzione] = useState(false);
  const [reparti, setReparti] = useState<Reparto[]>([]);
  const [filters, setFilters] = useState<Filters>({ linee: [], tipiDocumento: [] });

  // Form state — report analisi costi
  const [dataFrom, setDataFrom] = useState("");
  const [dataTo, setDataTo] = useState("");
  const [selectedReparto, setSelectedReparto] = useState<number | "">("");
  const [selectedTipoDocumento, setSelectedTipoDocumento] = useState("");
  const [selectedLinea, setSelectedLinea] = useState("");
  const [groupBy, setGroupBy] = useState<"reparto" | "linea" | "tipoDocumento" | "mese">("reparto");
  const [includeDetails, setIncludeDetails] = useState(false);
  const [includeArticoliPerReparto, setIncludeArticoliPerReparto] = useState(false);
  const [showUncorrelatedCosts, setShowUncorrelatedCosts] = useState(false);
  const [showCostoTomaia, setShowCostoTomaia] = useState(false);

  // Form state — report produzione mese
  const now = new Date();
  const [prodAnno, setProdAnno] = useState<number>(now.getFullYear());
  const [prodMese, setProdMese] = useState<number>(now.getMonth() + 1);
  const [prodTipoDocumento, setProdTipoDocumento] = useState("");
  const [prodLinea, setProdLinea] = useState("");
  const [includeProduzione, setIncludeProduzione] = useState(false);

  // Form state — report reparto origine
  const [generatingOrigine, setGeneratingOrigine] = useState(false);
  const [origineDataFrom, setOrigineDataFrom] = useState("");
  const [origineDataTo, setOrigineDataTo] = useState("");
  const [selectedRepartoOrigine, setSelectedRepartoOrigine] = useState<number | "">("");
  const [origineTipoDocumento, setOrigineTipoDocumento] = useState("");
  const [origineLinea, setOrigineLinea] = useState("");
  const [origineGroupBy, setOrigineGroupBy] = useState<"reparto" | "linea" | "tipoDocumento" | "mese">("reparto");
  const [origineIncludeArticoli, setOrigineIncludeArticoli] = useState(false);
  const [origineShowUncorrelated, setOrigineShowUncorrelated] = useState(false);
  const [origineIncludeDetails, setOrigineIncludeDetails] = useState(false);

  useEffect(() => {
    fetchInitialData();
  }, []);

  const fetchInitialData = async () => {
    try {
      setLoading(true);
      const [repartiData, filtersData] = await Promise.all([
        analiticheApi.getReparti(true),
        analiticheApi.getFilters(),
      ]);
      setReparti(repartiData || []);
      // Map the filter data - backend returns { value, count } objects
      setFilters({
        linee: (filtersData?.linee || []).map((item: any) => item.value || item),
        tipiDocumento: (filtersData?.tipiDocumento || []).map((item: any) => item.value || item),
      });
    } catch (error) {
      showError("Errore nel caricamento dei dati");
    } finally {
      setLoading(false);
    }
  };

  const handleGeneratePdf = async () => {
    try {
      setGenerating(true);
      await analiticheApi.generatePdfReport({
        dataFrom: dataFrom || undefined,
        dataTo: dataTo || undefined,
        repartoId: selectedReparto !== "" ? Number(selectedReparto) : undefined,
        tipoDocumento: selectedTipoDocumento || undefined,
        linea: selectedLinea || undefined,
        groupBy,
        includeArticoliPerReparto,
        showUncorrelatedCosts,
        showCostoTomaia,
      });
      showSuccess("Il lavoro è stato messo in coda.");
    } catch (error: any) {
      showError(error?.response?.data?.message || "Errore nella generazione del report PDF");
    } finally {
      setGenerating(false);
    }
  };

  const handleGenerateExcel = async () => {
    try {
      setGenerating(true);
      await analiticheApi.generateExcelReport({
        dataFrom: dataFrom || undefined,
        dataTo: dataTo || undefined,
        repartoId: selectedReparto !== "" ? Number(selectedReparto) : undefined,
        tipoDocumento: selectedTipoDocumento || undefined,
        linea: selectedLinea || undefined,
        includeDetails,
        showUncorrelatedCosts,
        showCostoTomaia,
      });
      showSuccess("Il lavoro è stato messo in coda.");
    } catch (error: any) {
      showError(error?.response?.data?.message || "Errore nella generazione del report Excel");
    } finally {
      setGenerating(false);
    }
  };

  const handleGenerateProduzionePdf = async () => {
    try {
      setGeneratingProduzione(true);
      await analiticheApi.generateProduzionePdfReport({
        anno: prodAnno,
        mese: prodMese,
        tipoDocumento: prodTipoDocumento || undefined,
        linea: prodLinea || undefined,
        includeProduzione,
      });
      showSuccess("Il lavoro è stato messo in coda.");
    } catch (error: any) {
      showError(error?.response?.data?.message || "Errore nella generazione del report Produzione PDF");
    } finally {
      setGeneratingProduzione(false);
    }
  };

  const handleGenerateProduzioneExcel = async () => {
    try {
      setGeneratingProduzione(true);
      await analiticheApi.generateProduzioneExcelReport({
        anno: prodAnno,
        mese: prodMese,
        tipoDocumento: prodTipoDocumento || undefined,
        linea: prodLinea || undefined,
        includeProduzione,
      });
      showSuccess("Il lavoro è stato messo in coda.");
    } catch (error: any) {
      showError(error?.response?.data?.message || "Errore nella generazione del report Produzione Excel");
    } finally {
      setGeneratingProduzione(false);
    }
  };

  const handleGenerateOriginePdf = async () => {
    try {
      setGeneratingOrigine(true);
      await analiticheApi.generateOriginePdfReport({
        dataFrom: origineDataFrom || undefined,
        dataTo: origineDataTo || undefined,
        repartoOrigineId: selectedRepartoOrigine !== "" ? Number(selectedRepartoOrigine) : undefined,
        tipoDocumento: origineTipoDocumento || undefined,
        linea: origineLinea || undefined,
        groupBy: origineGroupBy,
        includeArticoliPerReparto: origineIncludeArticoli,
        showUncorrelatedCosts: origineShowUncorrelated,
      });
      showSuccess("Il lavoro è stato messo in coda.");
    } catch (error: any) {
      showError(error?.response?.data?.message || "Errore nella generazione del report Origine PDF");
    } finally {
      setGeneratingOrigine(false);
    }
  };

  const handleGenerateOrigineExcel = async () => {
    try {
      setGeneratingOrigine(true);
      await analiticheApi.generateOrigineExcelReport({
        dataFrom: origineDataFrom || undefined,
        dataTo: origineDataTo || undefined,
        repartoOrigineId: selectedRepartoOrigine !== "" ? Number(selectedRepartoOrigine) : undefined,
        tipoDocumento: origineTipoDocumento || undefined,
        linea: origineLinea || undefined,
        includeDetails: origineIncludeDetails,
        showUncorrelatedCosts: origineShowUncorrelated,
      });
      showSuccess("Il lavoro è stato messo in coda.");
    } catch (error: any) {
      showError(error?.response?.data?.message || "Errore nella generazione del report Origine Excel");
    } finally {
      setGeneratingOrigine(false);
    }
  };

  const resetFilters = () => {
    setDataFrom("");
    setDataTo("");
    setSelectedReparto("");
    setSelectedTipoDocumento("");
    setSelectedLinea("");
    setGroupBy("reparto");
    setIncludeDetails(false);
    setIncludeArticoliPerReparto(false);
    setShowUncorrelatedCosts(false);
    setShowCostoTomaia(false);
  };

  if (loading) {
    return (
      <div className="flex h-64 items-center justify-center">
        <motion.div
          animate={{ rotate: 360 }}
          transition={{ duration: 1, repeat: Infinity, ease: "linear" }}
          className="h-12 w-12 rounded-full border-4 border-solid border-emerald-500 border-t-transparent"
        />
      </div>
    );
  }

  // ---- helpers per input class ----
  const inputCls = (focus: string) =>
    `w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-${focus}-500 focus:ring-${focus}-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white`;

  const MESI = ["","Gennaio","Febbraio","Marzo","Aprile","Maggio","Giugno","Luglio","Agosto","Settembre","Ottobre","Novembre","Dicembre"];

  return (
    <motion.div initial="hidden" animate="visible" variants={containerVariants}>
      <PageHeader
        title="Reportistica Analitiche"
        subtitle="Genera report PDF ed Excel dei dati analitici"
      />

      <Breadcrumb
        items={[
          { label: "Dashboard", href: "/", icon: "fa-home" },
          { label: "Analitiche", href: "/analitiche" },
          { label: "Reportistica" },
        ]}
      />

      {/* ==================== ANALISI PER REPARTO FINALE ==================== */}
      <motion.div variants={itemVariants} className="mb-6">
        <div className="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm overflow-hidden">
          <div className="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 className="flex items-center gap-3 text-base font-semibold text-gray-900 dark:text-white">
              <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500">
                <i className="fas fa-flag-checkered text-white text-sm"></i>
              </span>
              Analisi per Reparto Finale
            </h3>
            <button onClick={resetFilters} className="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
              <i className="fas fa-undo mr-1"></i>Reset
            </button>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-0">
            {/* Filtri */}
            <div className="lg:col-span-2 p-6 border-r border-gray-100 dark:border-gray-700">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Data Da</label>
                  <input type="date" value={dataFrom} onChange={(e) => setDataFrom(e.target.value)}
                    className={inputCls("blue")} />
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Data A</label>
                  <input type="date" value={dataTo} onChange={(e) => setDataTo(e.target.value)}
                    className={inputCls("blue")} />
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Reparto Finale</label>
                  <select value={selectedReparto} onChange={(e) => setSelectedReparto(e.target.value === "" ? "" : Number(e.target.value))}
                    className={inputCls("blue")}>
                    <option value="">Tutti i reparti</option>
                    {reparti.map((r) => <option key={r.id} value={r.id}>{r.nome}{r.codice ? ` (${r.codice})` : ""}</option>)}
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tipo Documento</label>
                  <select value={selectedTipoDocumento} onChange={(e) => setSelectedTipoDocumento(e.target.value)}
                    className={inputCls("blue")}>
                    <option value="">Tutti i tipi</option>
                    {filters.tipiDocumento.map((t) => <option key={t} value={t}>{t}</option>)}
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Linea</label>
                  <select value={selectedLinea} onChange={(e) => setSelectedLinea(e.target.value)}
                    className={inputCls("blue")}>
                    <option value="">Tutte le linee</option>
                    {filters.linee.map((l) => <option key={l} value={l}>{l}</option>)}
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Raggruppa per (PDF)</label>
                  <select value={groupBy} onChange={(e) => setGroupBy(e.target.value as any)}
                    className={inputCls("blue")}>
                    <option value="reparto">Reparto</option>
                    <option value="linea">Linea</option>
                    <option value="tipoDocumento">Tipo Documento</option>
                    <option value="mese">Mese</option>
                  </select>
                </div>
              </div>
              <div className="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 grid grid-cols-1 md:grid-cols-2 gap-2">
                {[
                  { checked: showUncorrelatedCosts, setter: setShowUncorrelatedCosts, icon: "fa-exclamation-triangle text-amber-500", label: "Mostra costi non correlati ai reparti" },
                  { checked: showCostoTomaia,       setter: setShowCostoTomaia,       icon: "fa-layer-group text-purple-500",         label: "Mostra Costo Tomaia (prodotti esteri)" },
                  { checked: includeArticoliPerReparto, setter: setIncludeArticoliPerReparto, icon: "fa-file-pdf text-red-500",      label: "Dettaglio articoli per reparto (PDF)" },
                  { checked: includeDetails,        setter: setIncludeDetails,        icon: "fa-file-excel text-emerald-500",         label: "Includi dettaglio record (Excel)" },
                ].map(({ checked, setter, icon, label }) => (
                  <label key={label} className="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" checked={checked} onChange={(e) => setter(e.target.checked)}
                      className="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                    <span className="text-xs text-gray-700 dark:text-gray-300">
                      <i className={`fas ${icon} mr-1`}></i>{label}
                    </span>
                  </label>
                ))}
              </div>
            </div>
            {/* Azioni */}
            <div className="p-6 flex flex-col gap-3 justify-center bg-gray-50/50 dark:bg-gray-800/20">
              <button onClick={handleGeneratePdf} disabled={generating}
                className="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-semibold shadow hover:from-red-600 hover:to-red-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                {generating ? <i className="fas fa-spinner fa-spin"></i> : <i className="fas fa-file-pdf"></i>}
                <span>Genera PDF</span>
              </button>
              <button onClick={handleGenerateExcel} disabled={generating}
                className="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 text-white text-sm font-semibold shadow hover:from-emerald-600 hover:to-emerald-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                {generating ? <i className="fas fa-spinner fa-spin"></i> : <i className="fas fa-file-excel"></i>}
                <span>Genera Excel</span>
              </button>
            </div>
          </div>
        </div>
      </motion.div>

      {/* ==================== ANALISI PER REPARTO ORIGINE ==================== */}
      <motion.div variants={itemVariants} className="mb-6">
        <div className="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm overflow-hidden">
          <div className="flex items-center px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 className="flex items-center gap-3 text-base font-semibold text-gray-900 dark:text-white">
              <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-green-600">
                <i className="fas fa-map-marker-alt text-white text-sm"></i>
              </span>
              Analisi per Reparto Origine
            </h3>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-0">
            <div className="lg:col-span-2 p-6 border-r border-gray-100 dark:border-gray-700">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Data Da</label>
                  <input type="date" value={origineDataFrom} onChange={(e) => setOrigineDataFrom(e.target.value)}
                    className={inputCls("green")} />
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Data A</label>
                  <input type="date" value={origineDataTo} onChange={(e) => setOrigineDataTo(e.target.value)}
                    className={inputCls("green")} />
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Reparto Origine</label>
                  <select value={selectedRepartoOrigine} onChange={(e) => setSelectedRepartoOrigine(e.target.value === "" ? "" : Number(e.target.value))}
                    className={inputCls("green")}>
                    <option value="">Tutti i reparti</option>
                    {reparti.map((r) => <option key={r.id} value={r.id}>{r.nome}{r.codice ? ` (${r.codice})` : ""}</option>)}
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tipo Documento</label>
                  <select value={origineTipoDocumento} onChange={(e) => setOrigineTipoDocumento(e.target.value)}
                    className={inputCls("green")}>
                    <option value="">Tutti i tipi</option>
                    {filters.tipiDocumento.map((t) => <option key={t} value={t}>{t}</option>)}
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Linea</label>
                  <select value={origineLinea} onChange={(e) => setOrigineLinea(e.target.value)}
                    className={inputCls("green")}>
                    <option value="">Tutte le linee</option>
                    {filters.linee.map((l) => <option key={l} value={l}>{l}</option>)}
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Raggruppa per (PDF)</label>
                  <select value={origineGroupBy} onChange={(e) => setOrigineGroupBy(e.target.value as any)}
                    className={inputCls("green")}>
                    <option value="reparto">Reparto Origine</option>
                    <option value="linea">Linea</option>
                    <option value="tipoDocumento">Tipo Documento</option>
                    <option value="mese">Mese</option>
                  </select>
                </div>
              </div>
              <div className="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 grid grid-cols-1 md:grid-cols-2 gap-2">
                {[
                  { checked: origineShowUncorrelated, setter: setOrigineShowUncorrelated, icon: "fa-exclamation-triangle text-amber-500", label: "Mostra costi non correlati ai reparti" },
                  { checked: origineIncludeArticoli,  setter: setOrigineIncludeArticoli,  icon: "fa-file-pdf text-red-500",              label: "Dettaglio articoli per reparto (PDF)" },
                  { checked: origineIncludeDetails,   setter: setOrigineIncludeDetails,   icon: "fa-file-excel text-emerald-500",         label: "Includi dettaglio record (Excel)" },
                ].map(({ checked, setter, icon, label }) => (
                  <label key={label} className="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" checked={checked} onChange={(e) => setter(e.target.checked)}
                      className="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500" />
                    <span className="text-xs text-gray-700 dark:text-gray-300">
                      <i className={`fas ${icon} mr-1`}></i>{label}
                    </span>
                  </label>
                ))}
              </div>
            </div>
            <div className="p-6 flex flex-col gap-3 justify-center bg-gray-50/50 dark:bg-gray-800/20">
              <button onClick={handleGenerateOriginePdf} disabled={generatingOrigine}
                className="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-semibold shadow hover:from-red-600 hover:to-red-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                {generatingOrigine ? <i className="fas fa-spinner fa-spin"></i> : <i className="fas fa-file-pdf"></i>}
                <span>Genera PDF</span>
              </button>
              <button onClick={handleGenerateOrigineExcel} disabled={generatingOrigine}
                className="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-r from-green-600 to-emerald-700 text-white text-sm font-semibold shadow hover:from-green-700 hover:to-emerald-800 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                {generatingOrigine ? <i className="fas fa-spinner fa-spin"></i> : <i className="fas fa-file-excel"></i>}
                <span>Genera Excel</span>
              </button>
            </div>
          </div>
        </div>
      </motion.div>

      {/* ==================== REPORT PRODUZIONE MESE ==================== */}
      <motion.div variants={itemVariants} className="mb-6">
        <div className="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm overflow-hidden">
          <div className="flex items-center px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 className="flex items-center gap-3 text-base font-semibold text-gray-900 dark:text-white">
              <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500">
                <i className="fas fa-calendar-alt text-white text-sm"></i>
              </span>
              Report Produzione Mese
            </h3>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-0">
            <div className="lg:col-span-2 p-6 border-r border-gray-100 dark:border-gray-700">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Anno</label>
                  <input type="number" min={2020} max={2099} value={prodAnno}
                    onChange={(e) => setProdAnno(Number(e.target.value))}
                    className={inputCls("indigo")} />
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Mese</label>
                  <select value={prodMese} onChange={(e) => setProdMese(Number(e.target.value))}
                    className={inputCls("indigo")}>
                    {MESI.slice(1).map((m, i) => <option key={i + 1} value={i + 1}>{m}</option>)}
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                    Tipo Documento <span className="text-gray-400 font-normal">(opzionale)</span>
                  </label>
                  <select value={prodTipoDocumento} onChange={(e) => setProdTipoDocumento(e.target.value)}
                    className={inputCls("indigo")}>
                    <option value="">Tutti i tipi</option>
                    {filters.tipiDocumento.map((t) => <option key={t} value={t}>{t}</option>)}
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                    Linea <span className="text-gray-400 font-normal">(opzionale)</span>
                  </label>
                  <select value={prodLinea} onChange={(e) => setProdLinea(e.target.value)}
                    className={inputCls("indigo")}>
                    <option value="">Tutte le linee</option>
                    {filters.linee.map((l) => <option key={l} value={l}>{l}</option>)}
                  </select>
                </div>
              </div>
              <div className="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 space-y-2">
                <label className="flex items-center gap-2 cursor-pointer">
                  <input type="checkbox" checked={includeProduzione} onChange={(e) => setIncludeProduzione(e.target.checked)}
                    className="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                  <span className="text-xs text-gray-700 dark:text-gray-300">
                    <i className="fas fa-industry text-indigo-500 mr-1"></i>
                    Includi dati di produzione (griglia affiancata)
                  </span>
                </label>
                {includeProduzione && (
                  <p className="ml-6 text-xs text-indigo-500 dark:text-indigo-400">
                    Aggiunge una seconda griglia con le paia prodotte, basata sulla mappatura reparti configurata in Impostazioni.
                  </p>
                )}
                <p className="text-xs text-gray-500 dark:text-gray-400 mt-2">
                  <i className="fas fa-info-circle text-indigo-400 mr-1"></i>
                  Solo record con Reparto Finale assegnato. Colonne = reparti attivi, righe = giorni del mese.
                </p>
              </div>
            </div>
            <div className="p-6 flex flex-col gap-3 justify-center bg-gray-50/50 dark:bg-gray-800/20">
              <p className="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide">
                <i className="fas fa-table mr-1"></i>
                {MESI[prodMese]} {prodAnno}
              </p>
              <button onClick={handleGenerateProduzionePdf} disabled={generatingProduzione}
                className="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-semibold shadow hover:from-red-600 hover:to-red-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                {generatingProduzione ? <i className="fas fa-spinner fa-spin"></i> : <i className="fas fa-file-pdf"></i>}
                <span>Genera PDF</span>
              </button>
              <button onClick={handleGenerateProduzioneExcel} disabled={generatingProduzione}
                className="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 text-white text-sm font-semibold shadow hover:from-indigo-600 hover:to-violet-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                {generatingProduzione ? <i className="fas fa-spinner fa-spin"></i> : <i className="fas fa-file-excel"></i>}
                <span>Genera Excel</span>
              </button>
            </div>
          </div>
        </div>
      </motion.div>
    </motion.div>
  );
}
