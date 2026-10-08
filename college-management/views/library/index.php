<?php
$title = 'Library & Resource Inventory - ' . setting('app_name', 'EduCore');
require VIEW_PATH . '/layout/header.php';

$bookItems = $books['data'] ?? $books['items'] ?? [];
$issueItems = $issues['data'] ?? $issues['items'] ?? [];
?>

<div class="page-header flex justify-between items-center mb-6">
    <div>
        <h1 class="page-title text-2xl font-bold">Library & Learning Resources</h1>
        <p class="text-muted text-sm">Catalog management, physical book lending inventory, student borrowing records, and automated fines.</p>
    </div>
    <div class="flex gap-2">
        <?php if (has_role('admin', 'operator')): ?>
            <button type="button" class="btn btn-primary" onclick="openModal('addBookModal')">
                <i class="fas fa-plus-circle mr-1"></i> Add New Book
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="flex gap-2 mb-6 border-b border-border pb-3">
    <a href="<?= e(base_url('library?tab=books')) ?>" class="btn btn-sm <?= $currentTab !== 'issues' ? 'btn-primary' : 'btn-secondary' ?>">
        <i class="fas fa-books mr-1"></i> Books Catalog
    </a>
    <a href="<?= e(base_url('library?tab=issues')) ?>" class="btn btn-sm <?= $currentTab === 'issues' ? 'btn-primary' : 'btn-secondary' ?>">
        <i class="fas fa-hand-holding mr-1"></i> Lending & Circulation Records
    </a>
</div>

<?php if ($currentTab !== 'issues'): ?>
    <!-- Books Catalog View -->
    <div class="card p-4 mb-6">
        <form method="get" action="<?= e(base_url('library')) ?>" class="flex gap-3">
            <input type="hidden" name="tab" value="books">
            <input type="text" name="q" value="<?= e($search ?? '') ?>" placeholder="Search by title, author, ISBN or category..." class="form-control flex-1">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i> Search Catalog</button>
            <?php if (!empty($search)): ?>
                <a href="<?= e(base_url('library?tab=books')) ?>" class="btn btn-secondary"><i class="fas fa-times"></i></a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card p-0 mb-6">
        <div class="card-header p-4 flex justify-between items-center border-b border-border">
            <span class="badge badge-primary font-bold px-2.5 py-1">
                <?= (int)($books['total'] ?? count($bookItems)) ?> Registered Titles
            </span>
            <span class="text-xs text-muted">Page <?= (int)($books['page'] ?? 1) ?> of <?= (int)($books['last_page'] ?? 1) ?></span>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ISBN</th>
                        <th>Title & Author</th>
                        <th>Category</th>
                        <th>Rack / Shelf</th>
                        <th class="text-center">Total Copies</th>
                        <th class="text-center">Available</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookItems)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-8 text-muted">
                                <i class="fas fa-book text-4xl mb-3 block opacity-30"></i>
                                No books found in library catalog.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookItems as $b): ?>
                            <tr>
                                <td><span class="badge badge-neutral font-mono font-bold text-2xs"><?= e($b['isbn']) ?></span></td>
                                <td>
                                    <div class="font-bold text-text"><?= e($b['title']) ?></div>
                                    <div class="text-xs text-muted">by <?= e($b['author']) ?></div>
                                </td>
                                <td><span class="badge badge-primary text-2xs font-semibold"><?= e($b['category']) ?></span></td>
                                <td class="text-xs text-muted font-mono"><i class="fas fa-map-marker-alt mr-1 text-primary"></i> <?= e($b['rack_location'] ?? 'General Section') ?></td>
                                <td class="text-center font-mono font-bold text-text"><?= (int)$b['total_copies'] ?></td>
                                <td class="text-center font-mono">
                                    <?php if ((int)$b['available_copies'] > 0): ?>
                                        <span class="badge badge-success font-bold"><?= (int)$b['available_copies'] ?> in stock</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger font-bold">Out of stock</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <?php if (has_role('admin', 'operator', 'faculty')): ?>
                                        <?php if ((int)$b['available_copies'] > 0): ?>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="openIssueModal(<?= (int)$b['id'] ?>, '<?= e(addslashes($b['title'])) ?>')">
                                                <i class="fas fa-hand-holding mr-1"></i> Issue
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-secondary" disabled>Unavailable</button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php else: ?>
    <!-- Lending & Circulation Records View -->
    <div class="card p-0 mb-6">
        <div class="card-header p-4 flex justify-between items-center border-b border-border">
            <span class="badge badge-primary font-bold px-2.5 py-1">
                <?= (int)($issues['total'] ?? count($issueItems)) ?> Circulation Records
            </span>
            <div class="flex gap-2">
                <a href="<?= e(base_url('library?tab=issues')) ?>" class="btn btn-sm <?= empty($currentStatus) ? 'btn-primary' : 'btn-secondary' ?>">All</a>
                <a href="<?= e(base_url('library?tab=issues&status=issued')) ?>" class="btn btn-sm <?= $currentStatus === 'issued' ? 'btn-primary' : 'btn-secondary' ?>">Currently Lent</a>
                <a href="<?= e(base_url('library?tab=issues&status=returned')) ?>" class="btn btn-sm <?= $currentStatus === 'returned' ? 'btn-primary' : 'btn-secondary' ?>">Returned</a>
            </div>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Book Information</th>
                        <th>Borrowing Student</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Overdue Fine</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($issueItems)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-8 text-muted">
                                <i class="fas fa-clipboard-list text-4xl mb-3 block opacity-30"></i>
                                No lending circulation records logged yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($issueItems as $item): ?>
                            <?php 
                            $isOverdue = ($item['status'] === 'issued' && strtotime($item['due_date']) < time());
                            ?>
                            <tr>
                                <td>
                                    <div class="font-bold text-text"><?= e($item['book_title']) ?></div>
                                    <div class="text-xs text-muted font-mono"><?= e($item['isbn']) ?></div>
                                </td>
                                <td>
                                    <div class="font-bold text-text"><?= e($item['student_name']) ?></div>
                                    <div class="text-xs text-muted font-mono"><?= e($item['admission_no']) ?></div>
                                </td>
                                <td class="text-xs text-muted"><?= date('M d, Y', strtotime($item['issue_date'])) ?></td>
                                <td class="text-xs <?= $isOverdue ? 'text-rose-500 font-bold' : 'text-muted' ?>">
                                    <?= date('M d, Y', strtotime($item['due_date'])) ?>
                                </td>
                                <td class="text-xs text-muted">
                                    <?= !empty($item['return_date']) ? date('M d, Y', strtotime($item['return_date'])) : '&mdash;' ?>
                                </td>
                                <td>
                                    <?php if ($item['status'] === 'returned'): ?>
                                        <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Returned</span>
                                    <?php elseif ($isOverdue): ?>
                                        <span class="badge badge-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Overdue</span>
                                    <?php else: ?>
                                        <span class="badge badge-primary"><i class="fas fa-book-reader mr-1"></i> Issued</span>
                                    <?php endif; ?>
                                </td>
                                <td class="font-mono text-xs">
                                    <?php if ((float)$item['fine_amount'] > 0): ?>
                                        <span class="text-rose-500 font-bold"><?= format_currency((float)$item['fine_amount']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">$0.00</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <?php if ($item['status'] === 'issued' && has_role('admin', 'operator', 'faculty')): ?>
                                        <form method="post" action="<?= e(base_url('library/' . $item['id'] . '/return')) ?>" class="inline-block" onsubmit="return confirm('Process return of this book?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-undo mr-1"></i> Return Book
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted text-xs">Completed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php if (has_role('admin', 'operator')): ?>
<!-- Add Book Modal -->
<div id="addBookModal" class="modal-overlay hidden">
    <div class="modal-content max-w-lg">
        <div class="modal-header flex justify-between items-center border-b border-border pb-3 mb-4">
            <h3 class="text-lg font-bold"><i class="fas fa-book text-primary mr-2"></i>Add Book to Inventory</h3>
            <button type="button" class="btn-close text-muted hover:text-text" onclick="closeModal('addBookModal')">&times;</button>
        </div>
        <form method="post" action="<?= e(base_url('library/books')) ?>">
            <?= csrf_field() ?>
            <div class="modal-body space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">ISBN *</label>
                        <input type="text" name="isbn" class="form-control font-mono" placeholder="978-0131103627" required>
                    </div>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Category *</label>
                        <input type="text" name="category" class="form-control" placeholder="Computer Science, Physics, Law" required>
                    </div>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Book Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="Introduction to Algorithms (CLRS)" required>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Author(s) *</label>
                    <input type="text" name="author" class="form-control" placeholder="Thomas H. Cormen, Charles E. Leiserson" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Total Physical Copies *</label>
                        <input type="number" name="total_copies" class="form-control" value="5" min="1" max="100" required>
                    </div>
                    <div>
                        <label class="form-label text-xs uppercase font-semibold">Rack / Shelf Location</label>
                        <input type="text" name="rack_location" class="form-control" placeholder="Shelf B-04">
                    </div>
                </div>
            </div>
            <div class="modal-footer flex justify-end gap-2 border-t border-border pt-3 mt-4">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addBookModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-check mr-1"></i> Add to Catalog</button>
            </div>
        </form>
    </div>
</div>

<!-- Issue Book Modal -->
<div id="issueBookModal" class="modal-overlay hidden">
    <div class="modal-content max-w-md">
        <div class="modal-header flex justify-between items-center border-b border-border pb-3 mb-4">
            <h3 class="text-lg font-bold"><i class="fas fa-hand-holding text-primary mr-2"></i>Issue Book to Student</h3>
            <button type="button" class="btn-close text-muted hover:text-text" onclick="closeModal('issueBookModal')">&times;</button>
        </div>
        <form method="post" action="<?= e(base_url('library/issue')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" id="issue_book_id" name="book_id" value="">
            <div class="modal-body space-y-4">
                <div>
                    <label class="form-label text-xs uppercase font-semibold">Selected Book</label>
                    <input type="text" id="issue_book_title" class="form-control bg-background" readonly>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Borrowing Student *</label>
                    <select name="student_id" class="form-control" required>
                        <option value="">-- Choose Student --</option>
                        <?php foreach ($students as $s): ?>
                            <option value="<?= (int)$s['id'] ?>">
                                <?= e($s['admission_no'] . ' - ' . $s['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="form-label text-xs uppercase font-semibold">Return Due Date *</label>
                    <input type="date" name="due_date" class="form-control" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" required>
                    <span class="text-2xs text-muted">Standard 14-day loan period. $1.00/day fine thereafter.</span>
                </div>
            </div>
            <div class="modal-footer flex justify-end gap-2 border-t border-border pt-3 mt-4">
                <button type="button" class="btn btn-secondary" onclick="closeModal('issueBookModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-check mr-1"></i> Confirm Lending</button>
            </div>
        </form>
    </div>
</div>

<script>
function openIssueModal(bookId, bookTitle) {
    document.getElementById('issue_book_id').value = bookId;
    document.getElementById('issue_book_title').value = bookTitle;
    openModal('issueBookModal');
}
</script>
<?php endif; ?>

<?php require VIEW_PATH . '/layout/footer.php'; ?>
