<?php $title = '404 Not Found - ' . setting('app_name', 'EduCore'); require VIEW_PATH . '/layout/header.php'; ?>
<div class="card p-12 text-center max-w-lg mx-auto my-12">
    <div class="w-20 h-20 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center text-4xl mx-auto mb-4">
        <i class="fas fa-compass"></i>
    </div>
    <h1 class="text-3xl font-bold text-text mb-2">404 Page Not Found</h1>
    <p class="text-muted text-sm mb-6">The URL route or module you requested could not be located on this college portal.</p>
    <a href="<?= e(base_url('/')) ?>" class="btn btn-primary">
        <i class="fas fa-home mr-1"></i> Return to Dashboard
    </a>
</div>
<?php require VIEW_PATH . '/layout/footer.php'; ?>
