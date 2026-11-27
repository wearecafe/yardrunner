import { describe, expect, it } from 'vite-plus/test';
import { isEdgeCachedPath } from '@/lib/edge-cached-pages';

describe('isEdgeCachedPath', () => {
    it('covers the home page and the help center', () => {
        expect(isEdgeCachedPath('/')).toBe(true);
        expect(isEdgeCachedPath('/help')).toBe(true);
        expect(isEdgeCachedPath('/help/selling/quotes')).toBe(true);
        expect(isEdgeCachedPath('/help/search')).toBe(true);
    });

    it('leaves app pages to Inertia', () => {
        expect(isEdgeCachedPath('/login')).toBe(false);
        expect(isEdgeCachedPath('/dashboard')).toBe(false);
        expect(isEdgeCachedPath('/helpdesk')).toBe(false);
        expect(isEdgeCachedPath('/settings/help')).toBe(false);
    });
});
