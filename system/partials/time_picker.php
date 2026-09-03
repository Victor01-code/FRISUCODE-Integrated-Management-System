<?php
$timePickerEmail = $timePickerEmail ?? '';
?>
<style>
.time-picker-container {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(226, 232, 240, 0.8);
    border-radius: 16px;
    padding: 15px 20px;
    margin-bottom: 25px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}
.time-picker-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: flex;
    align-items: center;
    gap: 8px;
}
.time-picker-pills {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.time-pill {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}
.time-pill:hover {
    background: #f1f5f9;
    transform: translateY(-1px);
}
.time-pill.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}
.time-picker-label {
    margin-top: 12px;
    font-size: 0.8rem;
    color: #94a3b8;
    font-weight: 500;
}
/* Counter animation */
.val-animate {
    transition: all 0.3s ease;
}
</style>

<div class="time-picker-container fade-in">
    <div class="time-picker-title"><i class="fa-solid fa-chart-pie"></i> <?= __('Analysis Period') ?></div>
    <div class="time-picker-pills">
        <button class="time-pill" data-period="today"><?= __('Today') ?></button>
        <button class="time-pill" data-period="week"><?= __('This Week') ?></button>
        <button class="time-pill" data-period="month"><?= __('This Month') ?></button>
        <button class="time-pill" data-period="year"><?= __('This Year') ?></button>
        <button class="time-pill" data-period="3year"><?= __('3 Years') ?></button>
        <button class="time-pill" data-period="5year"><?= __('5 Years') ?></button>
        <button class="time-pill active" data-period="all"><?= __('All Time') ?></button>
    </div>
    <div class="time-picker-label" id="time-picker-date-range"><?= __('Showing all records') ?></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pills = document.querySelectorAll('.time-pill');
    const dateRangeLabel = document.getElementById('time-picker-date-range');
    const donorEmail = "<?= htmlspecialchars($timePickerEmail) ?>";
    
    const formatNumber = (num) => {
        return num.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    };

    const animateValue = (element, start, end, isCurrency) => {
        if (!element) return;
        const duration = 800;
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const ease = 1 - Math.pow(1 - progress, 4);
            const current = start + (end - start) * ease;
            
            element.textContent = (isCurrency ? '$' : '') + formatNumber(current);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                element.textContent = (isCurrency ? '$' : '') + formatNumber(end);
            }
        };
        window.requestAnimationFrame(step);
    };

    const fetchStats = async (period) => {
        try {
            const url = `../api_dashboard_stats.php?period=${period}${donorEmail ? '&email='+encodeURIComponent(donorEmail) : ''}`;
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.success) {
                if (period === 'all') {
                    dateRangeLabel.textContent = "<?= __('Showing all records') ?>";
                } else {
                    dateRangeLabel.textContent = `<?= __('Showing data from') ?> ${data.date_from} — ${data.date_to}`;
                }

                const elements = [
                    { id: 'stat-donations', val: data.donations_total, currency: true },
                    { id: 'stat-donations-count', val: data.donations_count, currency: false },
                    { id: 'stat-expenses', val: data.expenses_total, currency: true },
                    { id: 'stat-income', val: data.income_total, currency: true },
                    { id: 'stat-net-balance', val: Math.abs(data.net_balance), currency: true },
                    { id: 'stat-active-programs', val: data.active_programs, currency: false },
                    { id: 'stat-budget', val: data.budget_total, currency: true }
                ];

                elements.forEach(item => {
                    const el = document.getElementById(item.id);
                    if (el) {
                        const currentVal = parseFloat(el.getAttribute('data-val') || 0);
                        animateValue(el, currentVal, item.val, item.currency);
                        el.setAttribute('data-val', item.val);
                        
                        if(item.id === 'stat-net-balance') {
                           if(data.net_balance < 0) {
                               el.style.color = '#dc2626';
                               el.textContent = '-$' + formatNumber(Math.abs(data.net_balance));
                           } else {
                               if (data.net_balance >= 0) {
                                   el.style.color = '#4ade80'; // the original color in super_admin
                                   el.textContent = (data.net_balance > 0 ? '+$' : '$') + formatNumber(data.net_balance);
                               }
                           }
                        }
                    }
                });
            }
        } catch (error) {
            console.error('Error fetching stats:', error);
        }
    };

    pills.forEach(pill => {
        pill.addEventListener('click', function() {
            pills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            fetchStats(this.getAttribute('data-period'));
        });
    });
});
</script>
