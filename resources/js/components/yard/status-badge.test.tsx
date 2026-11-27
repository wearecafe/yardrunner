import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vite-plus/test';
import { OrderStatusBadge, PaymentBadge } from '@/components/yard/status-badge';

describe('OrderStatusBadge', () => {
    it('says a released rental is on rent', () => {
        render(<OrderStatusBadge status="fulfilled" rental />);
        expect(screen.getByText('On rent')).toBeInTheDocument();
    });

    it('says a released goods order is released', () => {
        render(<OrderStatusBadge status="fulfilled" />);
        expect(screen.getByText('Released')).toBeInTheDocument();
    });
});

describe('PaymentBadge', () => {
    it('says an unpaid order on account is on account, not unpaid', () => {
        render(<PaymentBadge status="unpaid" settlement="account" />);
        expect(screen.getByText('On account')).toBeInTheDocument();
        expect(screen.queryByText('Unpaid')).not.toBeInTheDocument();
    });

    it('says a part-paid order is part paid', () => {
        render(<PaymentBadge status="partial" settlement={null} />);
        expect(screen.getByText('Part paid')).toBeInTheDocument();
    });
});
