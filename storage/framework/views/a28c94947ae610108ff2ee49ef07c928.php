<?php $__env->startSection('title', 'Website Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="dashboard-content">
    <h2 class="mb-4">Website Settings</h2>

    <!-- Success/Error Messages -->
    <div id="alert-container">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Title Sistem Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Update Title Sistem</h5>
        </div>
        <div class="card-body">
            <form id="titleSistemForm" action="<?php echo e(route('settings.update.title_sistem')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label for="title_sistem" class="form-label">Title Sistem</label>
                    <input type="text" class="form-control" id="title_sistem" name="title_sistem" value="<?php echo e($setting->title_sistem); ?>" required>
                    <?php $__errorArgs = ['title_sistem'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <small class="text-danger"><?php echo e($message); ?></small>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>

    <!-- Nama Perusahaan Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Update Nama Perusahaan</h5>
        </div>
        <div class="card-body">
            <form id="namaPerusahaanForm" action="<?php echo e(route('settings.update.nama_perusahaan')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                    <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" value="<?php echo e($setting->nama_perusahaan); ?>" required>
                    <?php $__errorArgs = ['nama_perusahaan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <small class="text-danger"><?php echo e($message); ?></small>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>

    <!-- Alamat Perusahaan Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Update Alamat Perusahaan</h5>
        </div>
        <div class="card-body">
            <form id="alamatPerusahaanForm" action="<?php echo e(route('settings.update.alamat_perusahaan')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label for="alamat_perusahaan" class="form-label">Alamat Perusahaan</label>
                    <textarea class="form-control" id="alamat_perusahaan" name="alamat_perusahaan" rows="4" required><?php echo e($setting->alamat_perusahaan); ?></textarea>
                    <?php $__errorArgs = ['alamat_perusahaan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <small class="text-danger"><?php echo e($message); ?></small>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>

    <!-- Nomor WhatsApp Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Update Nomor WhatsApp</h5>
        </div>
        <div class="card-body">
            <form id="nomorWaForm" action="<?php echo e(route('settings.update.nomor_wa')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label for="nomor_wa" class="form-label">Nomor WhatsApp</label>
                    <input type="text" class="form-control" id="nomor_wa" name="nomor_wa" value="<?php echo e($setting->nomor_wa); ?>" pattern="\+?[0-9]{10,20}" required>
                    <?php $__errorArgs = ['nomor_wa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <small class="text-danger"><?php echo e($message); ?></small>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>

    <!-- Banner Management -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Manage Banners</h5>
        </div>
        <div class="card-body">
            <!-- Banner Form -->
            <form id="bannerForm" action="<?php echo e(route('settings.store.banner')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" id="banner_id">
                <div class="mb-3">
                    <label for="banner_image" class="form-label">Banner Image</label>
                    <input type="file" class="form-control" id="banner_image" name="image" accept="image/*">
                    <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <small class="text-danger"><?php echo e($message); ?></small>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="mb-3">
                    <label for="banner_title" class="form-label">Banner Title</label>
                    <input type="text" class="form-control" id="banner_title" name="title" required>
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <small class="text-danger"><?php echo e($message); ?></small>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="mb-3">
                    <label for="banner_description" class="form-label">Banner Description</label>
                    <textarea class="form-control" id="banner_description" name="description" rows="4"></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <small class="text-danger"><?php echo e($message); ?></small>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="mb-3">
                    <label for="is_active" class="form-label">Status</label>
                    <select class="form-control" id="is_active" name="is_active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" id="banner_submit">Add Banner</button>
            </form>

            <!-- Banner List -->
            <div class="mt-4">
                <h5>Banner List</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <?php if($banner->image): ?>
                                        <img src="<?php echo e(asset('banners/' . $banner->image)); ?>" alt="<?php echo e($banner->title); ?>" style="max-width: 100px;">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($banner->title); ?></td>
                                <td><?php echo e($banner->description); ?></td>
                                <td><?php echo e($banner->is_active ? 'Active' : 'Inactive'); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-banner" data-id="<?php echo e($banner->id); ?>" data-title="<?php echo e($banner->title); ?>" data-description="<?php echo e($banner->description); ?>" data-is_active="<?php echo e($banner->is_active); ?>">Edit</button>
                                    <form action="<?php echo e(route('settings.delete.banner', $banner->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this banner?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Admin Management -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Manage Admins</h5>
        </div>
        <div class="card-body">
            <!-- Admin Form -->
            <form id="adminForm" action="<?php echo e(route('settings.store.admin')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" id="admin_id">
                <div class="mb-3">
                    <label for="admin_name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="admin_name" name="name" required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <small class="text-danger"><?php echo e($message); ?></small>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="mb-3">
                    <label for="admin_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="admin_email" name="email" required>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <small class="text-danger"><?php echo e($message); ?></small>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="mb-3">
                    <label for="admin_password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="admin_password" name="password">
                    <small class="text-muted">Leave blank to keep the current password when updating.</small>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <small class="text-danger"><?php echo e($message); ?></small>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="mb-3">
                    <label for="admin_is_active" class="form-label">Status</label>
                    <select class="form-control" id="admin_is_active" name="is_active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" id="admin_submit">Add Admin</button>
            </form>

            <!-- Admin List -->
            <div class="mt-4">
                <h5>Admin List</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($admin->name); ?></td>
                                <td><?php echo e($admin->email); ?></td>
                                <td><?php echo e($admin->is_active ? 'Active' : 'Inactive'); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-admin" data-id="<?php echo e($admin->id); ?>" data-name="<?php echo e($admin->name); ?>" data-email="<?php echo e($admin->email); ?>" data-is_active="<?php echo e($admin->is_active); ?>">Edit</button>
                                    <form action="<?php echo e(route('settings.delete.admin', $admin->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this admin?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check for CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        console.error('CSRF token not found. Please add <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"> to the layout.');
        alert('CSRF token is missing. Please contact the administrator.');
        return;
    }

    // Function to show alerts
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alert-container');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.setAttribute('role', 'alert');
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        alertContainer.innerHTML = '';
        alertContainer.appendChild(alert);
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }, 3000);
    }

    // Function to handle form submission
    function handleFormSubmission(formId, action) {
        const form = document.getElementById(formId);
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);

            fetch(action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    if (formId === 'bannerForm' || formId === 'adminForm') {
                        window.location.reload(); // Reload to update list
                    }
                } else {
                    showAlert('danger', data.message || 'Failed to update setting');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred. Please try again.');
            });
        });
    }

    // Attach form submission handlers
    handleFormSubmission('titleSistemForm', '<?php echo e(route('settings.update.title_sistem')); ?>');
    handleFormSubmission('namaPerusahaanForm', '<?php echo e(route('settings.update.nama_perusahaan')); ?>');
    handleFormSubmission('alamatPerusahaanForm', '<?php echo e(route('settings.update.alamat_perusahaan')); ?>');
    handleFormSubmission('nomorWaForm', '<?php echo e(route('settings.update.nomor_wa')); ?>');
    handleFormSubmission('bannerForm', '<?php echo e(route('settings.store.banner')); ?>');
    handleFormSubmission('adminForm', '<?php echo e(route('settings.store.admin')); ?>');

    // Client-side validation for nomor_wa
    const nomorWaInput = document.getElementById('nomor_wa');
    nomorWaInput.addEventListener('input', function() {
        const value = this.value.trim();
        if (!/^\+?[0-9]{10,20}$/.test(value)) {
            this.setCustomValidity('Nomor WhatsApp must be a valid phone number (10-20 digits, optional + prefix).');
        } else {
            this.setCustomValidity('');
        }
    });

    // Handle banner edit
    document.querySelectorAll('.edit-banner').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const title = this.getAttribute('data-title');
            const description = this.getAttribute('data-description');
            const is_active = this.getAttribute('data-is_active');

            document.getElementById('banner_id').value = id;
            document.getElementById('banner_title').value = title;
            document.getElementById('banner_description').value = description;
            document.getElementById('is_active').value = is_active;
            document.getElementById('banner_submit').textContent = 'Update Banner';
            document.getElementById('bannerForm').action = '<?php echo e(route('settings.update.banner')); ?>';
        });
    });

    // Handle admin edit
    document.querySelectorAll('.edit-admin').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const email = this.getAttribute('data-email');
            const is_active = this.getAttribute('data-is_active');

            document.getElementById('admin_id').value = id;
            document.getElementById('admin_name').value = name;
            document.getElementById('admin_email').value = email;
            document.getElementById('admin_is_active').value = is_active;
            document.getElementById('admin_password').value = ''; // Clear password field
            document.getElementById('admin_submit').textContent = 'Update Admin';
            document.getElementById('adminForm').action = '<?php echo e(route('settings.update.admin')); ?>';
        });
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('template.admin-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\AdminTiketFerryLegend\resources\views/admin/setting.blade.php ENDPATH**/ ?>