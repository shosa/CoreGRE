import { SetMetadata } from '@nestjs/common';

export const PERMISSIONS_KEY = 'permissions';
export const PERM_ACTION_KEY = 'perm_action';

/** Livelli di accesso per moduli operativi (bitmask cumulativo) */
export const PERM = { NONE: 0, READ: 1, CREATE: 3, UPDATE: 7, DELETE: 15 } as const;
export type PermLevel = 0 | 1 | 3 | 7 | 15;

/**
 * Specifica i moduli richiesti per accedere alla route.
 * Usage: @RequirePermissions('riparazioni')
 */
export const RequirePermissions = (...permissions: string[]) =>
  SetMetadata(PERMISSIONS_KEY, permissions);

/**
 * Specifica il livello minimo di permesso richiesto per la route (moduli operativi).
 * Usage: @RequirePermLevel(PERM.CREATE)
 */
export const RequirePermLevel = (level: PermLevel) =>
  SetMetadata(PERM_ACTION_KEY, level);

/**
 * Controlla se un valore numerico soddisfa il livello richiesto.
 * Ogni livello ha un bit dedicato: READ=bit0, CREATE=bit1, UPDATE=bit2, DELETE=bit3
 */
export function hasPermLevel(userValue: number, required: number): boolean {
  if (required === PERM.READ)   return (userValue & 1) !== 0;
  if (required === PERM.CREATE) return (userValue & 2) !== 0;
  if (required === PERM.UPDATE) return (userValue & 4) !== 0;
  if (required === PERM.DELETE) return (userValue & 8) !== 0;
  return false;
}
