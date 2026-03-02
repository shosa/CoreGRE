import { create } from 'zustand';
import { persist, createJSONStorage } from 'zustand/middleware';

// Bitmask levels (must match backend PERM constants)
export const PERM = { NONE: 0, READ: 1, CREATE: 3, UPDATE: 7, DELETE: 15 } as const;
export type PermLevel = 0 | 1 | 3 | 7 | 15;

export function hasPermLevel(userValue: number | boolean | undefined, required: PermLevel): boolean {
  if (userValue === true) return true;   // legacy boolean → full access
  if (!userValue) return false;
  const val = userValue as number;
  if (required === PERM.READ)   return (val & 1) !== 0;
  if (required === PERM.CREATE) return (val & 2) !== 0;
  if (required === PERM.UPDATE) return (val & 4) !== 0;
  if (required === PERM.DELETE) return (val & 8) !== 0;
  return false;
}

interface User {
  id: number;
  userName: string;
  nome: string;
  mail: string;
  permissions: Record<string, boolean | number>;
}

interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  sidebarCollapsed: boolean;
  _hasHydrated: boolean;
  setAuth: (user: User, token: string) => void;
  logout: () => void;
  updateUser: (user: Partial<User>) => void;
  toggleSidebar: () => void;
  hasPermission: (module: string) => boolean;
  hasPermLevel: (module: string, level: PermLevel) => boolean;
  setHasHydrated: (state: boolean) => void;
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      user: null,
      token: null,
      isAuthenticated: false,
      sidebarCollapsed: false,
      _hasHydrated: false,

      setAuth: (user, token) => {
        set({ user, token, isAuthenticated: true });
      },

      logout: () => {
        set({ user: null, token: null, isAuthenticated: false });
      },

      updateUser: (userData) => {
        set((state) => ({
          user: state.user ? { ...state.user, ...userData } : null,
        }));
      },

      toggleSidebar: () => {
        set((state) => ({ sidebarCollapsed: !state.sidebarCollapsed }));
      },

      hasPermission: (module) => {
        const { user } = get();
        if (!user) return false;
        const val = user.permissions?.[module];
        if (typeof val === 'boolean') return val === true;
        if (typeof val === 'number') return val > 0;
        return false;
      },

      hasPermLevel: (module, level) => {
        const { user } = get();
        if (!user) return false;
        return hasPermLevel(user.permissions?.[module], level);
      },

      setHasHydrated: (state) => {
        set({ _hasHydrated: state });
      },
    }),
    {
      name: 'coregre-auth',
      storage: createJSONStorage(() => localStorage),
      partialize: (state) => ({
        user: state.user,
        token: state.token,
        isAuthenticated: state.isAuthenticated,
        sidebarCollapsed: state.sidebarCollapsed,
      }),
      onRehydrateStorage: () => (state) => {
        state?.setHasHydrated(true);
      },
    }
  )
);

// Hook per aspettare l'hydration
export const useHydration = () => {
  return useAuthStore((state) => state._hasHydrated);
};
