<div class="embed-responsive">
    <iframe title="Compara" class="powerbi-frame" src="https://app.powerbi.com/view?r=eyJrIjoiNThlMTBkYjctOGJmOC00NjY2LTk5YmQtNWNhZWJhYWMzNzIyIiwidCI6ImIzMTI4MDM5LTFkN2ItNGE0Ny1hYjA2LTE1MmU3MWMzYTg1NyIsImMiOjR9" frameborder="0" allowFullScreen="true"></iframe>
</div>

<style>
  .embed-responsive {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%; /* Proporción de aspecto 16:9 (ajusta esto según tu necesidad) */
    overflow: hidden;
  }

  .embed-responsive iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 60%;
  }

  @media print {
    .button {
      display: none;
    }
  }

  .button {
    float: right;
  }
</style>

<script>
  function imprime() {
    var css = '@page { size: landscape; }',
      head = document.head || document.getElementsByTagName('head')[0],
      style = document.createElement('style');

    style.type = 'text/css';
    style.media = 'print';

    if (style.styleSheet) {
      style.styleSheet.cssText = css;
    } else {
      style.appendChild(document.createTextNode(css));
    }

    head.appendChild(style);
    window.print();
  }
</script>

<!-- Reemplaza el botón por un enlace -->
<a href="javascript:void(0);" class="btn btn-success mb-2 button" onclick="imprime();">Imprimir</a>
