import {
  Injectable,
  CanActivate,
  ExecutionContext,
  ForbiddenException,
} from '@nestjs/common';
import { Reflector } from '@nestjs/core';
import { PrismaClient } from '@prisma/client';
import {
  PERMISSIONS_KEY,
  PERM_ACTION_KEY,
  PERM,
  hasPermLevel,
} from '../decorators/permissions.decorator';

@Injectable()
export class PermissionsGuard implements CanActivate {
  private prisma = new PrismaClient();
  private moduleCache: Map<string, { value: boolean; timestamp: number }> = new Map();
  private readonly CACHE_TTL = 60000; // 1 minute cache

  constructor(private reflector: Reflector) {}

  async canActivate(context: ExecutionContext): Promise<boolean> {
    const requiredPermissions = this.reflector.getAllAndOverride<string[]>(
      PERMISSIONS_KEY,
      [context.getHandler(), context.getClass()],
    );

    if (!requiredPermissions || requiredPermissions.length === 0) {
      return true;
    }

    const request = context.switchToHttp().getRequest();
    const user = request.user;

    if (!user || !user.userId) {
      throw new ForbiddenException('User not authenticated');
    }

    const userWithPermissions = await this.prisma.user.findUnique({
      where: { id: user.userId },
      include: { permissions: true },
    });

    if (!userWithPermissions) {
      throw new ForbiddenException('User not found');
    }

    // Check moduli abilitati
    for (const permission of requiredPermissions) {
      const moduleMap: Record<string, string> = { quality: 'qualita' };
      const moduleName = moduleMap[permission] || permission;
      const isModuleEnabled = await this.isModuleEnabled(moduleName);
      if (!isModuleEnabled) {
        throw new ForbiddenException(
          `Modulo "${permission}" non attivo. Contatta l'amministratore.`,
        );
      }
    }

    // Livello minimo richiesto per questo endpoint (default: READ)
    const requiredLevel: number =
      this.reflector.getAllAndOverride<number>(PERM_ACTION_KEY, [
        context.getHandler(),
        context.getClass(),
      ]) ?? PERM.READ;

    const userPermissions = userWithPermissions.permissions?.permessi || {};

    const granted = requiredPermissions.some((permission) => {
      const val = (userPermissions as Record<string, unknown>)[permission];
      if (typeof val === 'boolean') return val === true;
      if (typeof val === 'number')  return hasPermLevel(val, requiredLevel);
      return false;
    });

    if (!granted) {
      throw new ForbiddenException(
        `Accesso negato. Permessi richiesti: ${requiredPermissions.join(', ')}`,
      );
    }

    return true;
  }

  private async isModuleEnabled(moduleName: string): Promise<boolean> {
    const now = Date.now();
    const cached = this.moduleCache.get(moduleName);

    if (cached && now - cached.timestamp < this.CACHE_TTL) {
      return cached.value;
    }

    const setting = await this.prisma.setting.findUnique({
      where: { key: `module.${moduleName}.enabled` },
    });

    const isEnabled = setting?.value === 'true';
    this.moduleCache.set(moduleName, { value: isEnabled, timestamp: now });
    return isEnabled;
  }
}
