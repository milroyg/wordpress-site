document.addEventListener('DOMContentLoaded', function(){
    const docs = document.querySelectorAll('.my-doc');
    docs.forEach(doc => {
        loadFrameIfNotLoaded(doc);
    })

    function loadFrameIfNotLoaded(doc) {
        if (!doc) return false;
        const iframe = doc.querySelector("iframe");
        if (iframe && iframe.contentDocument !== null) {
          const source = iframe.src;
          iframe.src = source;
          setTimeout(() => {
            loadFrameIfNotLoaded(doc);
          }, 1000);
        }
    }
})