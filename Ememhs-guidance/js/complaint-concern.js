   // Show/hide other specify field based on complaint type selection
   document.getElementById('complaint_type').addEventListener('change', function() {
    const otherSpecifyGroup = document.getElementById('other_specify_group');
    if (this.value === 'others') {
        otherSpecifyGroup.style.display = 'block';
    } else {
        otherSpecifyGroup.style.display = 'none';
    }
});