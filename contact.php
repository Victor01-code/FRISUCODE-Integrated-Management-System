<?php include 'partials/header.php'; ?>

<div class="page-hero">
    <h1><?= ($L['contact_title'] ?? '') ?></h1>
    <p><?= ($L['contact_subtitle'] ?? '') ?></p>
</div>

<section style="padding: 80px 20px; max-width: 1200px; margin: auto;">
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 50px;">
        
        <!-- LEFT: Contact Details -->
        <div>
            <h2 style="font-size: 2.2rem; margin-bottom: 25px; line-height: 1.2;"><?= ($L['contact_title'] ?? '') ?></h2>
            <p style="color: #475569; font-size: 1.1rem; margin-bottom: 40px;">
                <?= ($L['contact_subtitle'] ?? '') ?>
            </p>

            <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                <div style="width: 50px; height: 50px; background: #eff6ff; color: #2563eb; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div>
                    <strong style="display: block; font-size: 1.1rem;"><?= ($L['email'] ?? '') ?></strong>
                    <a href="mailto:frisucode641@gmail.com" style="color: #64748b; text-decoration: none;">frisucode641@gmail.com</a>
                </div>
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                <div style="width: 50px; height: 50px; background: #fff7ed; color: #ea580c; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                    <i class="fa-solid fa-phone"></i>
                </div>
                <div>
                    <strong style="display: block; font-size: 1.1rem;"><?= ($L['phone'] ?? '') ?></strong>
                    <a href="tel:+255754917546" style="color: #64748b; text-decoration: none;">+255 754 917 546</a>
                </div>
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                <div style="width: 50px; height: 50px; background: #f0fdf4; color: #16a34a; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <div>
                    <strong style="display: block; font-size: 1.1rem;"><?= ($L['address'] ?? '') ?></strong>
                    <span style="color: #64748b;">Nambala, Kikwe, Arusha - Tanzania</span>
                </div>
            </div>

            <div style="margin-top: 40px; display: flex; gap: 15px;">
                <a href="#" style="width: 40px; height: 40px; background: #f1f5f9; color: #64748b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; transition: 0.3s;" onmouseover="this.style.background='#2563eb'; this.style.color='white'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b'">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
                <a href="#" style="width: 40px; height: 40px; background: #f1f5f9; color: #64748b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; transition: 0.3s;" onmouseover="this.style.background='#2563eb'; this.style.color='white'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b'">
                    <i class="fa-brands fa-instagram"></i>
                </a>
                <a href="#" style="width: 40px; height: 40px; background: #f1f5f9; color: #64748b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; transition: 0.3s;" onmouseover="this.style.background='#2563eb'; this.style.color='white'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b'">
                    <i class="fa-brands fa-linkedin-in"></i>
                </a>
            </div>
        </div>

        <!-- RIGHT: Inquiry Form -->
        <div style="background: white; padding: 40px; border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
            <h3 style="font-size: 1.5rem; margin-bottom: 25px;"><?= ($L['contact_title'] ?? '') ?></h3>
            <form action="#" method="POST">
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 8px; color: #475569;"><?= ($L['contact_name'] ?? '') ?></label>
                    <input type="text" name="name" placeholder="John Doe" required style="width: 100%; padding: 12px 15px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='#2563eb'; this.style.background='white'">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 8px; color: #475569;"><?= ($L['contact_email'] ?? '') ?></label>
                    <input type="email" name="email" placeholder="john.doe@example.com" required style="width: 100%; padding: 12px 15px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='#2563eb'; this.style.background='white'">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 8px; color: #475569;"><?= ($L['inquiry_subject'] ?? 'Inquiry Subject') ?></label>
                    <select name="subject" style="width: 100%; padding: 12px 15px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='#2563eb'; this.style.background='white'">
                        <option value="General Inquiry"><?= ($L['inquiry_general'] ?? 'General Inquiry') ?></option>
                        <option value="Sponsorship"><?= ($L['inquiry_sponsorship'] ?? 'Sponsorship Question') ?></option>
                        <option value="Volunteering"><?= ($L['inquiry_volunteering'] ?? 'Volunteering') ?></option>
                        <option value="Partnership"><?= ($L['inquiry_partnership'] ?? 'Partnership Proposal') ?></option>
                    </select>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="display: block; font-size: 0.9rem; font-weight: 700; margin-bottom: 8px; color: #475569;"><?= ($L['contact_message'] ?? '') ?></label>
                    <textarea name="message" rows="5" placeholder="..." required style="width: 100%; padding: 12px 15px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='#2563eb'; this.style.background='white'"></textarea>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; padding: 15px; border-radius: 12px; font-weight: 700; border: none; font-size: 1.1rem; cursor: pointer; transition: 0.3s;">
                    <?= ($L['contact_send'] ?? '') ?> <i class="fa-solid fa-paper-plane" style="margin-left: 8px;"></i>
                </button>
            </form>
        </div>

    </div>

</section>

<section style="padding: 0 20px 80px; max-width: 1200px; margin: auto; text-align: center;">
    <div class="map-container" style="width: 100%; height: 500px; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; position: relative; margin-bottom: 20px;">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15943.518!2d36.8206!3d-3.3768!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1837110013f9c647%3A0xbc5362a98f79339e!2sFrisucode!5e0!3m2!1sen!2stz!4v1712268000000!5m2!1sen!2stz" 
            width="100%" 
            height="100%" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
    
    <a href="https://maps.app.goo.gl/GL5Hw2KD75SEbPh2A" target="_blank" class="btn-primary" style="display: inline-flex; align-items: center; gap: 10px; text-decoration: none; padding: 12px 25px; border-radius: 50px;">
        <i class="fa-solid fa-map-location-dot"></i> View / Get Directions to Frisucode Office
    </a>
</section>

<?php include 'partials/footer.php'; ?>

