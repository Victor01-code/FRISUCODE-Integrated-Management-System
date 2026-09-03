<?php
require_once __DIR__ . '/../auth/auth_check.php';
requireRole(['super_admin', 'director', 'project_manager', 'me_officer', 'field_officer', 'finance']);
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header("Location: index.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) { header("Location: index.php?error=notfound"); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title      = trim($_POST['title'] ?? '');
    $content    = trim($_POST['content'] ?? '');
    $media_url  = trim($_POST['media_url'] ?? '');
    $media_type = $_POST['media_type'] ?? 'none';
    $pub_date   = $_POST['published_date'] ?? date('Y-m-d');
    $status     = $_POST['status'] ?? 'published';
    $category   = trim($_POST['category'] ?? 'General');
    $author     = trim($_POST['author'] ?? '');

    if (empty($title) || empty($content)) {
        $error = __('Title and content are required.');
    } else {
        // Handle file upload if present
        if (isset($_FILES['media_upload']) && $_FILES['media_upload']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../assets/uploads/news/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9.-]/', '_', basename($_FILES['media_upload']['name']));
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['media_upload']['tmp_name'], $targetPath)) {
                $media_url = '/frisucode_ms/public/assets/uploads/news/' . $fileName;
                // Auto-detect media type if not set properly
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (in_array($ext, ['mp4', 'webm', 'ogg'])) {
                    $media_type = 'video';
                } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $media_type = 'image';
                }
            }
        }
        
        // Handle extra media & attachment uploads
        $extra_media_1 = $_POST['extra_media_1'] ?? $item['extra_media_1'] ?? null;
        $extra_media_2 = $_POST['extra_media_2'] ?? $item['extra_media_2'] ?? null;
        $attachment_url = $_POST['attachment_url'] ?? $item['attachment_url'] ?? null;
        $attachment_name = trim($_POST['attachment_name'] ?? $item['attachment_name'] ?? '');
        
        $uploadDir = __DIR__ . '/../../assets/uploads/news/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        foreach (['extra_media_upload_1' => &$extra_media_1, 'extra_media_upload_2' => &$extra_media_2, 'attachment_upload' => &$attachment_url] as $key => &$var) {
            if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                $fName = time() . '_' . preg_replace('/[^a-zA-Z0-9.-]/', '_', basename($_FILES[$key]['name']));
                if (move_uploaded_file($_FILES[$key]['tmp_name'], $uploadDir . $fName)) {
                    $var = '/frisucode_ms/public/assets/uploads/news/' . $fName;
                    if ($key === 'attachment_upload' && empty($attachment_name)) {
                        $attachment_name = basename($_FILES[$key]['name']);
                    }
                }
            }
        }

        try {
            $stmt = $pdo->prepare("UPDATE news SET title=?, content=?, media_url=?, media_type=?, published_date=?, status=?, category=?, author=?, extra_media_1=?, extra_media_2=?, attachment_url=?, attachment_name=? WHERE id=?");
            $stmt->execute([$title, $content, $media_url ?: null, $media_url ? $media_type : 'none', $pub_date, $status, $category ?: 'General', $author ?: null, $extra_media_1 ?: null, $extra_media_2 ?: null, $attachment_url ?: null, $attachment_name ?: null, $id]);
            header("Location: index.php?saved=1");
            exit;
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
    // Re-populate with POST data on error
    $item = array_merge($item, $_POST);
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>FRISUCODE | Edit News Article</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/system-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="/frisucode_ms/assets/images/logo.png">
</head>
<body>

<div class="dashboard-layout">
    <?php renderSidebar(); ?>

    <div class="main">
        <?php include __DIR__ . '/../partials/header.php'; ?>

        <div class="page-header">
            <h2><i class="fa-solid fa-pen-to-square" style="color:var(--primary);margin-right:8px;"></i><?= __('Edit News Article') ?></h2>
            <a href="index.php" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> <?= __('Back to News') ?></a>
        </div>

        <div class="form-container fade-in">

            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data" id="newsForm">

                <div class="input-group">
                    <label><i class="fa-solid fa-heading"></i> <?= __('Article Title *') ?></label>
                    <input type="text" name="title" required placeholder="<?= __('e.g. School Renovation Completed in Nambala') ?>" value="<?= htmlspecialchars($item['title'] ?? '') ?>">
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label><i class="fa-solid fa-tag"></i> <?= __('Category') ?></label>
                        <select name="category">
                            <?php $cats = ['General','Education','Health','Community','Finance','Events']; ?>
                            <?php foreach ($cats as $cat): ?>
                                <option value="<?= $cat ?>" <?= (($item['category'] ?? 'General') === $cat) ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <label><i class="fa-solid fa-user-pen"></i> <?= __('Author Name') ?></label>
                        <input type="text" name="author" placeholder="<?= __('e.g. FRISUCODE Team') ?>" value="<?= htmlspecialchars($item['author'] ?? '') ?>">
                    </div>
                </div>

                <div class="input-group">
                    <label><i class="fa-solid fa-align-left"></i> <?= __('Article Content *') ?></label>
                    <textarea name="content" rows="10" required placeholder="<?= __('Write the full article content here...') ?>"><?= htmlspecialchars($item['content'] ?? '') ?></textarea>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label><i class="fa-solid fa-calendar-day"></i> <?= __('Publish Date') ?></label>
                        <input type="date" name="published_date" value="<?= htmlspecialchars($item['published_date'] ?? date('Y-m-d')) ?>">
                    </div>
                    <div class="input-group">
                        <label><i class="fa-solid fa-eye"></i> <?= __('Status') ?></label>
                        <select name="status">
                            <option value="published" <?= (($item['status'] ?? 'published') === 'published') ? 'selected' : '' ?>><?= __('Published') ?> – <?= __('Visible on website') ?></option>
                            <option value="draft" <?= (($item['status'] ?? '') === 'draft') ? 'selected' : '' ?>><?= __('Draft') ?> – <?= __('Hidden from public') ?></option>
                        </select>
                    </div>
                </div>

                <div class="input-group" style="margin-top:10px;">
                    <label><i class="fa-solid fa-photo-film"></i> <?= __('Media Type') ?></label>
                    <div class="radio-cards-grid">
                        <?php $mt = $item['media_type'] ?? 'none'; ?>
                        <label class="radio-card <?= $mt === 'none' ? 'active' : '' ?>">
                            <input type="radio" name="media_type" value="none" <?= $mt === 'none' ? 'checked' : '' ?> style="display:none;" onchange="toggleMediaUrl(this)">
                            <div class="icon-box"><i class="fa-solid fa-ban" style="color:#94a3b8;"></i></div>
                            <div><strong style="display:block;"><?= __('No Media') ?></strong><small><?= __('Text only') ?></small></div>
                        </label>
                        <label class="radio-card <?= $mt === 'image' ? 'active' : '' ?>">
                            <input type="radio" name="media_type" value="image" <?= $mt === 'image' ? 'checked' : '' ?> style="display:none;" onchange="toggleMediaUrl(this)">
                            <div class="icon-box"><i class="fa-solid fa-image" style="color:#2563eb;"></i></div>
                            <div><strong style="display:block;"><?= __('Image') ?></strong><small><?= __('Add an image URL') ?></small></div>
                        </label>
                        <label class="radio-card <?= $mt === 'video' ? 'active' : '' ?>">
                            <input type="radio" name="media_type" value="video" <?= $mt === 'video' ? 'checked' : '' ?> style="display:none;" onchange="toggleMediaUrl(this)">
                            <div class="icon-box"><i class="fa-solid fa-video" style="color:#dc2626;"></i></div>
                            <div><strong style="display:block;"><?= __('Video') ?></strong><small><?= __('Add a video URL') ?></small></div>
                        </label>
                    </div>
                </div>

                <div class="input-group" id="mediaUrlGroup" style="<?= $mt === 'none' ? 'display:none;' : '' ?>">
                    <label><i class="fa-solid fa-link"></i> <?= __('Media URL') ?></label>
                    <input type="url" name="media_url" id="mediaUrlInput" placeholder="https://example.com/image.jpg" value="<?= htmlspecialchars($item['media_url'] ?? '') ?>" style="margin-bottom:10px;">
                    
                    <div style="display:flex;align-items:center;margin:15px 0;">
                        <span style="flex:1;height:1px;background:#e2e8f0;"></span>
                        <span style="padding:0 15px;color:#94a3b8;font-size:0.85rem;font-weight:700;text-transform:uppercase;"><?= __('OR UPLOAD FROM DEVICE') ?></span>
                        <span style="flex:1;height:1px;background:#e2e8f0;"></span>
                    </div>

                    <label><i class="fa-solid fa-cloud-arrow-up"></i> <?= __('Upload File') ?></label>
                    <input type="file" name="media_upload" accept="image/*,video/*" style="padding:10px;border:2px dashed #cbd5e1;background:#f8fafc;width:100%;border-radius:12px;cursor:pointer;">
                    
                    <small style="color:#94a3b8;display:block;margin-top:8px;"><i class="fa-solid fa-info-circle"></i> <?= __('Enter the full URL OR choose a file to upload. Allowed types: Images, MP4.') ?></small>
                </div>

                <div style="margin-top:30px;padding-top:20px;border-top:1px solid #f1f5f9;">
                    <h4 style="margin-bottom:15px;color:#1e293b;"><i class="fa-solid fa-images" style="color:var(--primary);"></i> <?= __('Additional Content Media') ?></h4>
                    <p style="font-size:0.85rem;color:#64748b;margin-bottom:20px;"><?= __('These images/videos will be displayed between the article content automatically.') ?></p>
                    
                    <div class="form-row">
                        <div class="input-group">
                            <label><?= __('Extra Media 1') ?></label>
                            <input type="url" name="extra_media_1" placeholder="URL" value="<?= htmlspecialchars($item['extra_media_1'] ?? '') ?>" style="margin-bottom:10px;">
                            <input type="file" name="extra_media_upload_1" accept="image/*,video/*" style="padding:10px;border:2px dashed #cbd5e1;background:#f8fafc;width:100%;border-radius:12px;cursor:pointer;">
                        </div>
                        <div class="input-group">
                            <label><?= __('Extra Media 2') ?></label>
                            <input type="url" name="extra_media_2" placeholder="URL" value="<?= htmlspecialchars($item['extra_media_2'] ?? '') ?>" style="margin-bottom:10px;">
                            <input type="file" name="extra_media_upload_2" accept="image/*,video/*" style="padding:10px;border:2px dashed #cbd5e1;background:#f8fafc;width:100%;border-radius:12px;cursor:pointer;">
                        </div>
                    </div>
                </div>

                <div style="margin-top:20px;padding-top:20px;border-top:1px solid #f1f5f9;">
                    <h4 style="margin-bottom:15px;color:#1e293b;"><i class="fa-solid fa-file-pdf" style="color:#ef4444;"></i> <?= __('Attachment / Document') ?></h4>
                    <p style="font-size:0.85rem;color:#64748b;margin-bottom:20px;"><?= __('Attach a PDF or document for users to download (e.g. proof, full report, policy).') ?></p>
                    
                    <div class="form-row">
                        <div class="input-group">
                            <label><?= __('Attachment URL') ?></label>
                            <input type="url" name="attachment_url" placeholder="URL" value="<?= htmlspecialchars($item['attachment_url'] ?? '') ?>" style="margin-bottom:10px;">
                            <input type="file" name="attachment_upload" accept=".pdf,.doc,.docx,.zip" style="padding:10px;border:2px dashed #cbd5e1;background:#fef2f2;width:100%;border-radius:12px;cursor:pointer;">
                        </div>
                        <div class="input-group">
                            <label><?= __('Attachment Name') ?></label>
                            <input type="text" name="attachment_name" placeholder="<?= __('e.g. Full Financial Report Q1') ?>" value="<?= htmlspecialchars($item['attachment_name'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <div style="margin-top:40px;padding-top:20px;border-top:1px solid #f1f5f9;">
                    <button type="submit" class="btn-primary-block">
                        <i class="fa-solid fa-floppy-disk"></i> <?= __('Save Changes') ?>
                    </button>
                    <p style="text-align:center;font-size:0.85rem;color:#94a3b8;margin-top:20px;">
                        <i class="fa-solid fa-globe"></i> <?= __('Changes are reflected on the public website immediately if status is published.') ?>
                    </p>
                </div>

            </form>
        </div>

        <script>
        document.querySelectorAll('.radio-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.radio-card').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                const input = this.querySelector('input');
                input.checked = true;
                toggleMediaUrl(input);
            });
        });

        function toggleMediaUrl(radio) {
            const group = document.getElementById('mediaUrlGroup');
            if (radio.value === 'none') {
                group.style.display = 'none';
                document.getElementById('mediaUrlInput').value = '';
            } else {
                group.style.display = 'block';
            }
        }
        </script>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>
</div>

</body>
</html>
