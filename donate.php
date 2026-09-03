<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'partials/header.php';
?>

<div class="page-hero">
    <h1><?= ($L['donate_title'] ?? '') ?></h1>
    <p><?= ($L['donate_subtitle'] ?? '') ?></p>
</div>

<div class="donate-wrapper">
    <!-- Donation Form -->
    <div class="donate-form-container">
        
        <form action="donate_process.php" method="POST" class="donate-card">
            <h3>1. <?= ($L['choose_frequency'] ?? 'Choose Donation Frequency') ?></h3>
            <div class="donation-type">
                <label class="type-box active" onclick="selectType(this)">
                    <input type="radio" name="frequency" value="monthly" checked>
                    <strong><?= ($L['monthly'] ?? '') ?></strong>
                    <span class="badge"><?= ($L['monthly_badge'] ?? '') ?></span>
                </label>
                <label class="type-box" onclick="selectType(this)">
                    <input type="radio" name="frequency" value="once">
                    <strong><?= ($L['one_time'] ?? '') ?></strong>
                </label>
            </div>
            
            <h3 style="margin-top: 30px;"><?= ($L['choose_cause'] ?? '') ?></h3>
            <div class="cause-grid">
                <label class="cause-box active" onclick="selectCause(this)">
                    <input type="radio" name="cause" value="general" checked>
                    <strong><?= ($L['cause_flexible'] ?? '') ?></strong>
                </label>
                <label class="cause-box" onclick="selectCause(this)">
                    <input type="radio" name="cause" value="education">
                    <strong><?= ($L['cause_education'] ?? '') ?></strong>
                </label>
                <label class="cause-box" onclick="selectCause(this)">
                    <input type="radio" name="cause" value="infrastructure">
                    <strong><?= ($L['cause_infrastructure'] ?? '') ?></strong>
                </label>
                <label class="cause-box" onclick="selectCause(this)">
                    <input type="radio" name="cause" value="health">
                    <strong><?= ($L['cause_health'] ?? '') ?></strong>
                </label>
            </div>

            <h3 style="margin-top: 30px;"><?= ($L['donation_amount'] ?? '') ?> ($)</h3>
            <div class="amount-grid">
                <button type="button" onclick="setAmount(25, this)">$25</button>
                <button type="button" class="active" onclick="setAmount(50, this)">$50</button>
                <button type="button" onclick="setAmount(100, this)">$100</button>
                <button type="button" onclick="setAmount(250, this)">$250</button>
                <button type="button" onclick="setAmount(500, this)">$500</button>
                <input type="number" name="custom_amount" placeholder="<?= ($L['custom_amount'] ?? '') ?>" oninput="setCustomAmount(this.value)" style="padding: 10px; border: 2px solid #ddd; border-radius: 10px;">
                <input type="hidden" name="amount" id="finalAmount" value="50">
            </div>

            <h3 style="margin-top: 30px;"><?= ($L['payment_method'] ?? '') ?></h3>
            <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                <label style="flex: 1; border: 1px solid #ddd; padding: 10px; border-radius: 8px; cursor: pointer; text-align: center;" onclick="this.style.borderColor='#2563eb'">
                    <input type="radio" name="payment_method" value="card" checked> <br>
                    <i class="fa-solid fa-credit-card"></i> <br>
                    <small><?= ($L['card_payment'] ?? '') ?></small>
                </label>
                <label style="flex: 1; border: 1px solid #ddd; padding: 10px; border-radius: 8px; cursor: pointer; text-align: center;" onclick="this.style.borderColor='#2563eb'">
                    <input type="radio" name="payment_method" value="mobile"> <br>
                    <i class="fa-solid fa-mobile-screen"></i> <br>
                    <small><?= ($L['mpesa'] ?? '') ?></small>
                </label>
            </div>

            <h3 style="margin-top: 30px; margin-bottom: 20px;">5. <?= ($L['your_details'] ?? 'Your Details') ?></h3>
            
            <div class="form-name-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label><?= ($L['first_name'] ?? '') ?></label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="first_name" placeholder="John" required>
                    </div>
                </div>
                <div class="form-group">
                    <label><?= ($L['last_name'] ?? '') ?></label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="last_name" placeholder="Doe" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label><?= ($L['donor_email'] ?? '') ?></label>
                <div class="input-with-icon">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" placeholder="john.doe@example.com" required>
                </div>
            </div>

            <div class="form-group">
                <label><?= ($L['phone_optional'] ?? '') ?></label>
                <div class="input-with-icon">
                    <i class="fa-solid fa-phone"></i>
                    <input type="tel" name="phone" placeholder="+255 000 000 000">
                </div>
            </div>

            
            <button type="submit" class="btn-primary full-width" style="font-size: 18px; padding: 15px;">
                <?= ($L['donate_secure'] ?? '') ?> &rarr;
            </button>
            <p class="trust">🔒 256-bit SSL Secure Payment</p>

        </form>
    </div>

    <!-- Right Summary -->
    <div class="donate-summary">
        <h3 style="margin-bottom: 20px;">Your Impact</h3>
        <div class="summary-box">
            <div class="summary-row">
                <span>Frequency:</span>
                <strong id="summary-freq">Monthly</strong>
            </div>
            <div class="summary-row">
                <span>Cause:</span>
                <strong id="summary-cause">General Fund</strong>
            </div>
            <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
            <div class="summary-total">
                <span>Total:</span>
                <strong id="summary-total">$50.00</strong>
            </div>
        </div>
        
        <div class="impact-note" style="margin-top: 20px; font-size: 14px; color: #555; background: #eef2ff; padding: 15px; border-radius: 10px;">
            💡 <strong>Did you know?</strong><br>
            $50 can provide school uniforms and shoes for two children for an entire year.
        </div>
        
        <div style="margin-top: 30px;">
            <h4>Supported Payment Methods</h4>
            <div style="display: flex; gap: 10px; margin-top: 10px; font-size: 24px; color: #64748b;">
                <i class="fa-brands fa-cc-visa"></i>
                <i class="fa-brands fa-cc-mastercard"></i>
                <i class="fa-brands fa-cc-stripe"></i>
                <i class="fa-brands fa-cc-apple-pay"></i>
            </div>
            <ul style="list-style: none; padding: 0; font-size: 14px; color: #555; margin-top: 15px;">
                <li style="margin-bottom: 10px;">💳 <strong>Credit/Debit Card</strong> (Visa, Mastercard)</li>
                <li style="margin-bottom: 10px;">🏦 Bank Transfer (CRDB Bank)</li>
                <li style="margin-bottom: 10px;">📱 M-Pesa / Mobile Money</li>
                <li>📦 Supply Donations</li>
            </ul>
        </div>
    </div>
</div>

<script>
    function selectType(el) {
        document.querySelectorAll('.type-box').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
        const val = el.querySelector('input').value;
        document.getElementById('summary-freq').innerText = val.charAt(0).toUpperCase() + val.slice(1);
    }
    
    function selectCause(el) {
        document.querySelectorAll('.cause-box').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
        const val = el.querySelector('strong').innerText;
        document.getElementById('summary-cause').innerText = val;
    }
    
    function setAmount(amount, btn) {
        document.querySelectorAll('.amount-grid button').forEach(b => b.classList.remove('active'));
        if(btn) btn.classList.add('active');
        document.getElementById('finalAmount').value = amount;
        document.getElementById('summary-total').innerText = '$' + amount + '.00';
    }
    
    function setCustomAmount(val) {
        document.querySelectorAll('.amount-grid button').forEach(b => b.classList.remove('active'));
        if(val) {
            document.getElementById('finalAmount').value = val;
            document.getElementById('summary-total').innerText = '$' + val;
        }
    }
</script>

<?php include 'partials/footer.php'; ?>

