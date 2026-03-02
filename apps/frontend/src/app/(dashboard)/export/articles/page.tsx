'use client';

import { useEffect, useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { exportApi } from '@/lib/api';
import { showError, showSuccess } from '@/store/notifications';
import { useAuthStore, PERM } from '@/store/auth';
import PageHeader from '@/components/layout/PageHeader';
import Breadcrumb from '@/components/layout/Breadcrumb';

interface ArticleMaster {
  id: number;
  codiceArticolo: string;
  descrizione?: string;
  voceDoganale?: string;
  um?: string;
  prezzoUnitario?: number;
  createdAt?: string;
  updatedAt?: string;
}

const inputClass =
  'w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent';

const emptyForm = {
  codiceArticolo: '',
  descrizione: '',
  voceDoganale: '',
  um: '',
  prezzoUnitario: '',
};

export default function ExportArticlesPage() {
  const { hasPermLevel } = useAuthStore();
  const canCreate = hasPermLevel('export', PERM.CREATE);
  const canUpdate = hasPermLevel('export', PERM.UPDATE);
  const canDelete = hasPermLevel('export', PERM.DELETE);

  const [articles, setArticles] = useState<ArticleMaster[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');

  // Modal state
  const [showModal, setShowModal] = useState(false);
  const [modalMode, setModalMode] = useState<'create' | 'edit'>('create');
  const [selected, setSelected] = useState<ArticleMaster | null>(null);
  const [saving, setSaving] = useState(false);
  const [form, setForm] = useState(emptyForm);

  // Delete modal
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [toDelete, setToDelete] = useState<ArticleMaster | null>(null);

  useEffect(() => {
    load();
  }, []);

  const load = async (q?: string) => {
    setLoading(true);
    try {
      const data = await exportApi.getArticlesMaster(q);
      setArticles(data);
    } catch {
      showError('Errore nel caricamento degli articoli');
    } finally {
      setLoading(false);
    }
  };

  const handleSearch = (v: string) => {
    setSearch(v);
    load(v || undefined);
  };

  const openCreate = () => {
    setForm(emptyForm);
    setSelected(null);
    setModalMode('create');
    setShowModal(true);
  };

  const openEdit = (a: ArticleMaster) => {
    setSelected(a);
    setForm({
      codiceArticolo: a.codiceArticolo,
      descrizione: a.descrizione ?? '',
      voceDoganale: a.voceDoganale ?? '',
      um: a.um ?? '',
      prezzoUnitario: a.prezzoUnitario != null ? String(a.prezzoUnitario) : '',
    });
    setModalMode('edit');
    setShowModal(true);
  };

  const handleSave = async () => {
    if (!form.codiceArticolo.trim()) return;
    setSaving(true);
    const payload = {
      codiceArticolo: form.codiceArticolo.trim(),
      descrizione: form.descrizione.trim() || undefined,
      voceDoganale: form.voceDoganale.trim() || undefined,
      um: form.um.trim() || undefined,
      prezzoUnitario: form.prezzoUnitario ? parseFloat(form.prezzoUnitario) : undefined,
    };
    try {
      if (modalMode === 'create') {
        await exportApi.createArticleMaster(payload);
        showSuccess('Articolo creato');
      } else if (selected) {
        await exportApi.updateArticleMaster(selected.id, payload);
        showSuccess('Articolo aggiornato');
      }
      setShowModal(false);
      await load(search || undefined);
    } catch (e: any) {
      showError(e.response?.data?.message || 'Errore durante il salvataggio');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async () => {
    if (!toDelete) return;
    try {
      await exportApi.deleteArticleMaster(toDelete.id);
      showSuccess('Articolo eliminato');
      setShowDeleteModal(false);
      setToDelete(null);
      await load(search || undefined);
    } catch (e: any) {
      showError(e.response?.data?.message || "Errore durante l'eliminazione");
    }
  };

  const setField = (k: keyof typeof emptyForm) => (v: string) =>
    setForm((prev) => ({ ...prev, [k]: v }));

  return (
    <>
      <motion.div
        initial={{ opacity: 0, y: 10 }}
        animate={{ opacity: 1, y: 0 }}
        className="flex flex-col h-full"
      >
        <PageHeader
          title="Articoli Master"
          subtitle="Anagrafiche articoli, voci doganali, unità di misura e prezzi"
        />
        <Breadcrumb
          items={[
            { label: 'Dashboard', href: '/', icon: 'fa-home' },
            { label: 'Export', href: '/export' },
            { label: 'Articoli Master' },
          ]}
        />

        <div className="mt-4 flex-1 overflow-hidden rounded-2xl bg-white dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700 shadow flex flex-col">
          {/* Toolbar */}
          <div className="shrink-0 px-5 py-3.5 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center gap-3">
            <i className="fas fa-box text-orange-500 text-sm"></i>
            <span className="text-sm font-semibold text-gray-700 dark:text-gray-200">
              Articoli Master
            </span>
            {!loading && (
              <span className="text-xs text-gray-400">
                <span className="font-semibold text-gray-600 dark:text-gray-300">{articles.length}</span> articoli
              </span>
            )}

            {/* Search */}
            <div className="relative ml-2 flex-1 max-w-xs">
              <i className="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
              <input
                type="text"
                placeholder="Cerca per codice o descrizione..."
                value={search}
                onChange={(e) => handleSearch(e.target.value)}
                className="w-full pl-8 pr-8 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
              />
              {search && (
                <button
                  onClick={() => handleSearch('')}
                  className="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                >
                  <i className="fas fa-times-circle text-xs"></i>
                </button>
              )}
            </div>

            <div className="ml-auto flex items-center gap-2">
              {canCreate && (
                <button
                  onClick={openCreate}
                  className="flex items-center gap-2 rounded-lg bg-gradient-to-r from-orange-500 to-amber-600 px-3 py-2 text-xs font-medium text-white hover:shadow-md transition-all"
                >
                  <i className="fas fa-plus text-xs"></i>
                  Nuovo Articolo
                </button>
              )}
              <button
                onClick={() => load(search || undefined)}
                className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                title="Aggiorna"
              >
                <i className="fas fa-sync-alt text-xs"></i>
              </button>
            </div>
          </div>

          {/* Table */}
          <div className="flex-1 overflow-auto">
            {loading ? (
              <div className="flex h-40 items-center justify-center">
                <motion.div
                  animate={{ rotate: 360 }}
                  transition={{ duration: 1, repeat: Infinity, ease: 'linear' }}
                  className="h-8 w-8 rounded-full border-2 border-orange-500 border-t-transparent"
                />
              </div>
            ) : (
              <table className="w-full">
                <thead className="sticky top-0 bg-gray-50 dark:bg-gray-800 z-10">
                  <tr>
                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Codice</th>
                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Descrizione</th>
                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Voce Doganale</th>
                    <th className="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">U.M.</th>
                    <th className="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prezzo Unit.</th>
                    <th className="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Azioni</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
                  <AnimatePresence>
                    {articles.map((a, i) => (
                      <motion.tr
                        key={a.id}
                        initial={{ opacity: 0, x: -8 }}
                        animate={{ opacity: 1, x: 0 }}
                        exit={{ opacity: 0 }}
                        transition={{ delay: i * 0.02 }}
                        className="hover:bg-orange-50/40 dark:hover:bg-orange-900/10 transition-colors"
                      >
                        <td className="px-4 py-3 whitespace-nowrap">
                          <span className="font-mono text-sm font-bold text-orange-600 dark:text-orange-400">
                            {a.codiceArticolo}
                          </span>
                        </td>
                        <td className="px-4 py-3 max-w-[280px]">
                          <span className="text-sm text-gray-700 dark:text-gray-200 truncate block" title={a.descrizione}>
                            {a.descrizione || '—'}
                          </span>
                        </td>
                        <td className="px-4 py-3 whitespace-nowrap">
                          <span className="text-sm text-gray-500 dark:text-gray-400">
                            {a.voceDoganale || '—'}
                          </span>
                        </td>
                        <td className="px-4 py-3 whitespace-nowrap">
                          <span className="text-sm text-gray-500 dark:text-gray-400">
                            {a.um || '—'}
                          </span>
                        </td>
                        <td className="px-4 py-3 whitespace-nowrap text-right">
                          {a.prezzoUnitario != null ? (
                            <span className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                              € {Number(a.prezzoUnitario).toFixed(2)}
                            </span>
                          ) : (
                            <span className="text-sm text-gray-400">—</span>
                          )}
                        </td>
                        <td className="px-4 py-3 whitespace-nowrap text-right">
                          <div className="flex items-center justify-end gap-1.5">
                            {canUpdate && (
                              <button
                                onClick={() => openEdit(a)}
                                className="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors"
                                title="Modifica"
                              >
                                <i className="fas fa-edit text-xs"></i>
                              </button>
                            )}
                            {canDelete && (
                              <button
                                onClick={() => { setToDelete(a); setShowDeleteModal(true); }}
                                className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors"
                                title="Elimina"
                              >
                                <i className="fas fa-trash text-xs"></i>
                              </button>
                            )}
                          </div>
                        </td>
                      </motion.tr>
                    ))}
                  </AnimatePresence>
                </tbody>
              </table>
            )}

            {!loading && articles.length === 0 && (
              <div className="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500">
                <i className="fas fa-box-open text-4xl mb-3 opacity-40"></i>
                <p className="font-medium text-sm">Nessun articolo trovato</p>
                {search && <p className="text-xs mt-1">Prova a modificare la ricerca</p>}
                {!search && canCreate && (
                  <button
                    onClick={openCreate}
                    className="mt-4 flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600 transition-colors"
                  >
                    <i className="fas fa-plus text-xs"></i>
                    Crea il primo articolo
                  </button>
                )}
              </div>
            )}
          </div>
        </div>
      </motion.div>

      {/* Create / Edit Modal */}
      <AnimatePresence>
        {showModal && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.95 }}
              className="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800"
            >
              <h3 className="text-lg font-bold text-gray-900 dark:text-white mb-5">
                {modalMode === 'create' ? 'Nuovo Articolo Master' : 'Modifica Articolo Master'}
              </h3>

              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Codice Articolo <span className="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    value={form.codiceArticolo}
                    onChange={(e) => setField('codiceArticolo')(e.target.value)}
                    placeholder="Es. ART001"
                    className={inputClass}
                    autoFocus
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Descrizione
                  </label>
                  <input
                    type="text"
                    value={form.descrizione}
                    onChange={(e) => setField('descrizione')(e.target.value)}
                    placeholder="Descrizione articolo"
                    className={inputClass}
                  />
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                      Voce Doganale
                    </label>
                    <input
                      type="text"
                      value={form.voceDoganale}
                      onChange={(e) => setField('voceDoganale')(e.target.value)}
                      placeholder="Es. 6403.99"
                      className={inputClass}
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                      Unità di Misura
                    </label>
                    <input
                      type="text"
                      value={form.um}
                      onChange={(e) => setField('um')(e.target.value)}
                      placeholder="Es. PZ, KG, MT"
                      className={inputClass}
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Prezzo Unitario (€)
                  </label>
                  <input
                    type="number"
                    step="0.01"
                    min="0"
                    value={form.prezzoUnitario}
                    onChange={(e) => setField('prezzoUnitario')(e.target.value)}
                    placeholder="0.00"
                    className={inputClass}
                  />
                </div>
              </div>

              <div className="mt-6 flex gap-3">
                <button
                  onClick={() => setShowModal(false)}
                  disabled={saving}
                  className="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 disabled:opacity-50"
                >
                  Annulla
                </button>
                <button
                  onClick={handleSave}
                  disabled={saving || !form.codiceArticolo.trim()}
                  className="flex-1 rounded-lg bg-gradient-to-r from-orange-500 to-amber-600 px-4 py-2.5 text-sm font-medium text-white hover:shadow-md transition-all disabled:opacity-50"
                >
                  {saving ? 'Salvataggio...' : modalMode === 'create' ? 'Crea Articolo' : 'Salva Modifiche'}
                </button>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>

      {/* Delete Modal */}
      <AnimatePresence>
        {showDeleteModal && toDelete && (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.95 }}
              className="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800"
            >
              <div className="mb-4 flex items-center gap-3">
                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                  <i className="fas fa-exclamation-triangle text-xl text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                  <h3 className="text-lg font-bold text-gray-900 dark:text-white">Conferma Eliminazione</h3>
                  <p className="text-sm text-gray-500 dark:text-gray-400">Questa azione è irreversibile</p>
                </div>
              </div>
              <div className="mb-6 rounded-lg bg-gray-50 dark:bg-gray-900/50 p-4">
                <p className="text-sm text-gray-700 dark:text-gray-300">
                  Eliminare l&apos;articolo{' '}
                  <span className="font-bold text-orange-600 dark:text-orange-400">{toDelete.codiceArticolo}</span>
                  {toDelete.descrizione && (
                    <span className="text-gray-500"> — {toDelete.descrizione}</span>
                  )}
                  ?
                </p>
              </div>
              <div className="flex gap-3">
                <button
                  onClick={() => { setShowDeleteModal(false); setToDelete(null); }}
                  className="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                >
                  Annulla
                </button>
                <button
                  onClick={handleDelete}
                  className="flex-1 rounded-lg bg-gradient-to-r from-red-500 to-red-600 px-4 py-2.5 text-sm font-medium text-white hover:shadow-md transition-all"
                >
                  <i className="fas fa-trash mr-2"></i>Elimina
                </button>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </>
  );
}
