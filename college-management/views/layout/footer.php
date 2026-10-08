<?php if (is_authenticated()): ?>
        </div> <!-- .content-body -->
        <footer class="app-footer no-print" style="padding: 16px 32px; border-top: 1px solid var(--border); font-size: 0.8rem; color: var(--text-muted); display: flex; justify-content: space-between; align-items: center;">
            <div>&copy; <?= date('Y') ?> <?= e(setting('institute_name', config('name'))) ?>. All rights reserved.</div>
            <div>Academic Portal v2.0 • Powered by Secure PHP Architecture</div>
        </footer>
    </div> <!-- .app-main -->
</div> <!-- .app-wrapper -->
<?php else: ?>
    </main> <!-- .auth-page -->
<?php endif; ?>

<script src="<?= e(base_url('assets/js/app.js')) ?>"></script>
</body>
</html>
