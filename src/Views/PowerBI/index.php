<style>
    .pb-wrap {
        position: relative;
        width: 100%;
        height: calc(100vh - 80px);
    }
    .pb-wrap iframe {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        border: none;
    }
    @media print { #pb-print-btn { display: none; } }
</style>

<div class="pb-wrap">
    <iframe title="<?php echo htmlspecialchars($pbTitle); ?>"
            src="<?php echo htmlspecialchars($pbUrl); ?>"
            frameborder="0" allowfullscreen="true"></iframe>
</div>

<script>
(function(){
    var btn = document.createElement('a');
    btn.id = 'pb-print-btn';
    btn.href = 'javascript:void(0);';
    btn.className = 'btn btn-success btn-sm ml-2';
    btn.textContent = 'Imprimir';
    btn.onclick = function() {
        var style = document.createElement('style');
        style.media = 'print';
        style.appendChild(document.createTextNode('@page { size: landscape; }'));
        document.head.appendChild(style);
        window.print();
    };

    var footer = document.getElementById('footer');
    if (footer) {
        var container = footer.querySelector('.container');
        if (container) {
            container.style.display = 'flex';
            container.style.justifyContent = 'space-between';
            container.style.alignItems = 'center';
            container.appendChild(btn);
        }
    }
})();
</script>
