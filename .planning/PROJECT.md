# CoreGREJS UI Restyling

## What This Is

Restyling del design system del frontend di CoreGREJS — un'applicazione industriale per la gestione di produzione, qualità, riparazioni ed export. L'intervento agisce sul tema MUI, tipografia, colori primari e sidebar di navigazione per trasformare il look da Material Design standard a qualcosa di più moderno, pulito e professionale.

Solo `apps/frontend` è in scope. Backend, mobile e logica di business non sono toccati.

## Core Value

Una UI professionale e coerente che rende l'applicazione piacevole da usare ogni giorno — senza sacrificare funzionalità o performance.

## Requirements

### Validated

- ✓ Frontend funzionante con sidebar, autenticazione e tutte le pagine di modulo (produzione, quality, analitiche, export, riparazioni, ecc.) — esistente
- ✓ MUI 6 + Tailwind CSS 4 già configurati nel progetto — esistente
- ✓ Framer Motion disponibile per animazioni — esistente
- ✓ Sistema di permessi e navigazione funzionanti — esistente

### Active

- [ ] Nuovo tema MUI con palette blue/indigo moderna (stile SaaS contemporaneo)
- [ ] Tipografia rinnovata — font diverso con gerarchia più chiara
- [ ] Sidebar collassabile: icone quando collapsed, menu completo quando expanded
- [ ] Transizione animata della sidebar (Framer Motion)
- [ ] Approccio ibrido consolidato: MUI per DataGrid/DatePicker/form complessi, Tailwind per layout e stile generale
- [ ] Consistenza visiva su tutte le pagine dopo il cambio tema

### Out of Scope

- `apps/mobile` — mobile PWA esclusa, solo frontend web
- Modifiche al backend NestJS — nessun cambiamento API o DB
- Nuove funzionalità di business — solo UI, zero nuova logica
- OAuth, dark mode — fuori scope per questo milestone

## Context

- **Stack UI**: MUI 6.0.0 + Tailwind CSS 4.1.17 + Framer Motion 12 + Emotion
- **Framework**: Next.js 14.2 con App Router, React 18
- **Stato sidebar**: gestito in Zustand `useDashboardStore` — il toggle collapsed/expanded è già in store
- **Componenti layout**: `apps/frontend/src/components/layout/` (sidebar, header)
- **Tema MUI**: attualmente non customizzato — usa i default MUI blu
- **Pagine**: `apps/frontend/src/app/(dashboard)/` — produzione, quality, riparazioni, export, analitiche, tracking, settings
- **Stile attuale**: Mix di componenti MUI con Tailwind usato parzialmente per layout

## Constraints

- **Compatibilità**: Il restyling non deve rompere funzionalità esistenti — permessi, routing, form, tabelle restano invariati
- **Stack**: Nessuna nuova libreria UI — usare MUI + Tailwind già presenti
- **Colore primario**: Blue/indigo moderno (es. `indigo-600` / `#4F46E5` o simile)
- **MUI DataGrid**: Deve restare stilato con il tema MUI — non sostituire con alternativa Tailwind

## Key Decisions

| Decision | Rationale | Outcome |
|----------|-----------|---------|
| Design system first — customizzare tema MUI | Un tema custom si propaga automaticamente a tutti i componenti MUI senza toccare ogni pagina | — Pending |
| Sidebar collassabile in un'unica fase con il tema | Sidebar e tema insieme hanno il maggiore impatto visivo immediato | — Pending |
| Approccio ibrido MUI + Tailwind | MUI per componenti complessi (DataGrid, form), Tailwind per layout e stile custom | — Pending |
| Blue/indigo come colore primario | Look moderno stile SaaS, coerente con applicazioni enterprise contemporanee | — Pending |

---
*Last updated: 2026-03-03 after initialization*
