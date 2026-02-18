<!-- jQuery from CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<!-- Local scripts that exist -->
<script src="assets/js/apexcharts.min.js"></script>
<script src="assets/js/simplebar.min.js"></script>
<script src="assets/js/prism.js"></script>
<script src="assets/js/jsvectormap.min.js"></script>
<script src="assets/js/swiper-bundle.min.js"></script>
<script src="assets/js/quill.min.js"></script>

<!-- SweetAlert2 for modals -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- jsPDF for PDF generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- jsPDF AutoTable Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

<!-- CSV Export Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>

<!-- Excel Export Library (SheetJS) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<!-- Links Of JS File -->
<script src="assets/js/fslightbox.js"></script>
<script src="assets/js/clipboard.min.js"></script>
<script src="assets/js/fullcalendar.min.js"></script>
<script src="assets/js/world-merc.js"></script>
<script src="assets/js/custom.js"></script>

<!-- Select2 for enhanced dropdowns -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Slim Select (fallback) -->
<script src="https://cdn.jsdelivr.net/npm/slim-select@2.7.1/dist/slimselect.min.js"></script>

<!-- Selectize for enhanced inputs -->
<script src="https://cdn.jsdelivr.net/npm/selectize/dist/js/standalone/selectize.min.js"></script>

<!-- Profile Image Update Function -->
<script>
/**
 * Update profile images in header when user changes their profile picture
 * This function can be called from profile.php after successful image upload
 */
function updateHeaderProfileImages(profileImagePath) {
    console.log('Updating header profile images with path:', profileImagePath);
    
    // Get all profile images in the header
    const profileImages = document.querySelectorAll('img[alt="admin-image"]');
    
    profileImages.forEach(function(img) {
        if (profileImagePath && profileImagePath.trim() !== '') {
            // Normalize the path by replacing backslashes with forward slashes
            const normalizedPath = profileImagePath.replace(/\\/g, '/');
            const imageSrc = '../' + normalizedPath;
            
            // Set the new image source
            img.src = imageSrc;
            
            // Add error handling to fallback to default image if the new image fails to load
            img.onerror = function() {
                console.log('Profile image failed to load, using fallback');
                this.src = 'assets/images/users/3001764.png';
            };
            
            console.log('Updated profile image to:', imageSrc);
        } else {
            // If no profile image path provided, use default
            img.src = 'assets/images/users/3001764.png';
            console.log('Using default profile image');
        }
    });
}

/**
 * Refresh profile images from server data
 * This function fetches the latest profile data and updates the images
 */
function refreshHeaderProfileImages() {
    fetch('../api/auth/profile')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.user) {
                const profileImagePath = data.data.user.profile_picture;
                updateHeaderProfileImages(profileImagePath);
            }
        })
        .catch(error => {
            console.error('Error refreshing profile images:', error);
        });
}
</script>