document.addEventListener("DOMContentLoaded", function() {
  // Ensure we have files in drupalSettings
  if (typeof drupalSettings.adobe_field === 'undefined' || !Array.isArray(drupalSettings.adobe_field.files)) {
    console.error('No files available for preview.');
    return;
  }

  const settings = drupalSettings.adobe_field;
  const adobeDCViews = [];
  
  // Create the iframe for the first file preview
  const firstFile = settings.files[0];
  const adobeDCView = new AdobeDC.View({
    clientId: '99a4dd035df747d599c54077a2b252f5', // Replace with your Adobe DC View client ID
    divId: "adobe-dc-view-0"
  });
  adobeDCView.previewFile({
    content: { location: { url: firstFile.url } },
    metaData: { fileName: firstFile.filename }
  }, {
    defaultViewMode: "FIT_WIDTH",
    showAnnotationTools: false,
    showPrintPDF: false,
    showFullScreenViewButton: false
  });
  adobeDCViews.push(adobeDCView);

  // Setup the slider for additional files (from the second file onward)
  const slider = document.getElementById('adobe-slider');
  settings.files.slice(1).forEach(function(file, index) {
    const slideDiv = document.createElement('div');
    slideDiv.className = "adobe-slide";
    slideDiv.innerHTML = `<button class="adobe-slide-btn" data-index="${index + 1}">${file.filename}</button>`;
    slider.appendChild(slideDiv);
  });

  // Initialize the remaining iframes after a short delay
  setTimeout(function() {
    settings.files.slice(1).forEach(function(file, index) {
      const view = new AdobeDC.View({
        clientId: 'CLIENTIDHERE',
        divId: "adobe-dc-view-" + (index + 1)
      });
      view.previewFile({
        content: { location: { url: file.url } },
        metaData: { fileName: file.filename }
      }, {
        defaultViewMode: "FIT_WIDTH",
        showAnnotationTools: false,
        showPrintPDF: false,
        showFullScreenViewButton: false
      });
      adobeDCViews.push(view);
    });

    // Attach event listener for slider buttons
    document.querySelectorAll('.adobe-slide-btn').forEach(function(button) {
      button.addEventListener('click', function(event) {
        const selectedIndex = event.target.getAttribute('data-index');
        adobeDCViews.forEach(function(view, index) {
          if (index == selectedIndex) {
            view.show();
          } else {
            view.hide();
          }
        });
      });
    });
  }, 2000); // Delay of 2 seconds before initializing additional files

});
