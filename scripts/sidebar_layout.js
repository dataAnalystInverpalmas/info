/**
 * Inverpalmas 2026 - Modern Collapsible Sidebar Navigation Logic
 * Manages desktop toggle, mobile drawer, live filter, keyboard shortcuts, and DataTables adjustment.
 */
(function() {
  'use strict';

  // Apply stored preference immediately to prevent flash of uncollapsed layout
  try {
    var isCollapsed = localStorage.getItem('ip_sidebar_collapsed') === '1';
    if (isCollapsed && window.innerWidth >= 992) {
      document.body.classList.add('sidebar-collapsed');
    }
  } catch (e) {
    console.warn('LocalStorage error reading sidebar state:', e);
  }

  document.addEventListener('DOMContentLoaded', function() {
    var body = document.body;
    var toggleBtn = document.getElementById('sidebarToggleBtn');
    var closeBtn = document.getElementById('sidebarCloseBtn');
    var backdrop = document.getElementById('appSidebarBackdrop');
    var searchInput = document.getElementById('sidebarSearchInput');
    var searchClear = document.getElementById('sidebarSearchClear');
    var fullscreenBtn = document.getElementById('fullscreenToggleBtn');

    // Update toggle icon state on load
    function updateToggleIcon() {
      if (!toggleBtn) return;
      var icon = toggleBtn.querySelector('.material-icons');
      if (!icon) return;

      if (window.innerWidth >= 992) {
        if (body.classList.contains('sidebar-collapsed')) {
          icon.textContent = 'menu';
          toggleBtn.setAttribute('title', 'Mostrar menú lateral (Ctrl+B)');
        } else {
          icon.textContent = 'menu_open';
          toggleBtn.setAttribute('title', 'Ocultar menú lateral (Ctrl+B)');
        }
      } else {
        icon.textContent = 'menu';
        toggleBtn.setAttribute('title', 'Abrir menú');
      }
    }

    // Trigger DataTables & Charts recalculation after transition
    function triggerLayoutRecalculation() {
      setTimeout(function() {
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.dataTable) {
          try {
            window.jQuery.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
          } catch (err) {
            // silent ignore if no tables
          }
        }
        window.dispatchEvent(new Event('resize'));
      }, 260);
    }

    // Toggle Action
    function toggleSidebar() {
      if (window.innerWidth >= 992) {
        // Desktop
        var collapsed = body.classList.toggle('sidebar-collapsed');
        try {
          localStorage.setItem('ip_sidebar_collapsed', collapsed ? '1' : '0');
        } catch (e) {}
      } else {
        // Mobile / Tablet Drawer
        body.classList.toggle('sidebar-mobile-open');
      }
      updateToggleIcon();
      triggerLayoutRecalculation();
    }

    function closeMobileSidebar() {
      if (body.classList.contains('sidebar-mobile-open')) {
        body.classList.remove('sidebar-mobile-open');
        triggerLayoutRecalculation();
      }
    }

    if (toggleBtn) {
      toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        toggleSidebar();
      });
    }

    if (closeBtn) {
      closeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (window.innerWidth < 992) {
          closeMobileSidebar();
        } else {
          toggleSidebar();
        }
      });
    }

    if (backdrop) {
      backdrop.addEventListener('click', closeMobileSidebar);
    }

    // Keyboard shortcut: Ctrl+B or Cmd+B
    document.addEventListener('keydown', function(e) {
      if ((e.ctrlKey || e.metaKey) && (e.key === 'b' || e.key === 'B')) {
        // Don't trigger if user is typing inside an input or textarea
        var tag = (e.target && e.target.tagName) || '';
        if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') return;
        e.preventDefault();
        toggleSidebar();
      } else if (e.key === 'Escape') {
        closeMobileSidebar();
      }
    });

    // Close mobile sidebar on link click
    var sidebarLinks = document.querySelectorAll('.sidebar-sublink');
    sidebarLinks.forEach(function(link) {
      link.addEventListener('click', function() {
        if (window.innerWidth < 992) {
          closeMobileSidebar();
        }
      });
    });

    // Accordion functionality for groups
    var groupButtons = document.querySelectorAll('.sidebar-group-btn');
    groupButtons.forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        var parent = btn.closest('.sidebar-group');
        if (!parent) return;

        var isOpen = parent.classList.contains('is-open');
        parent.classList.toggle('is-open', !isOpen);
      });
    });

    // Real-time Live Menu Search Filter
    if (searchInput) {
      searchInput.addEventListener('input', function() {
        var query = this.value.trim().toLowerCase();

        if (searchClear) {
          searchClear.style.display = query.length > 0 ? 'inline-flex' : 'none';
        }

        var groups = document.querySelectorAll('.sidebar-group');
        if (!query) {
          // Restore default visibility
          groups.forEach(function(grp) {
            grp.style.display = '';
            var items = grp.querySelectorAll('.sidebar-subitem');
            items.forEach(function(item) { item.style.display = ''; });
            // Keep open only if it was originally active
            if (!grp.dataset.originallyOpen) {
              grp.classList.remove('is-open');
            } else {
              grp.classList.add('is-open');
            }
          });
          return;
        }

        // Filter items
        groups.forEach(function(grp) {
          var items = grp.querySelectorAll('.sidebar-subitem');
          var visibleCount = 0;

          items.forEach(function(item) {
            var text = item.textContent.toLowerCase();
            if (text.indexOf(query) !== -1) {
              item.style.display = '';
              visibleCount++;
            } else {
              item.style.display = 'none';
            }
          });

          if (visibleCount > 0) {
            grp.style.display = '';
            grp.classList.add('is-open'); // Auto-expand matching groups
          } else {
            grp.style.display = 'none';
          }
        });
      });

      if (searchClear) {
        searchClear.addEventListener('click', function(e) {
          e.preventDefault();
          searchInput.value = '';
          searchInput.dispatchEvent(new Event('input'));
          searchInput.focus();
        });
      }
    }

    // Mark originally open groups for search restoration
    document.querySelectorAll('.sidebar-group.is-open').forEach(function(grp) {
      grp.dataset.originallyOpen = '1';
    });

    // Fullscreen Toggle
    if (fullscreenBtn) {
      fullscreenBtn.addEventListener('click', function(e) {
        e.preventDefault();
        var icon = fullscreenBtn.querySelector('.material-icons');
        if (!document.fullscreenElement) {
          document.documentElement.requestFullscreen().then(function() {
            if (icon) icon.textContent = 'fullscreen_exit';
            fullscreenBtn.setAttribute('title', 'Salir de pantalla completa');
          }).catch(function() {});
        } else {
          if (document.exitFullscreen) {
            document.exitFullscreen().then(function() {
              if (icon) icon.textContent = 'fullscreen';
              fullscreenBtn.setAttribute('title', 'Pantalla completa');
            }).catch(function() {});
          }
        }
      });

      document.addEventListener('fullscreenchange', function() {
        var icon = fullscreenBtn.querySelector('.material-icons');
        if (!icon) return;
        icon.textContent = document.fullscreenElement ? 'fullscreen_exit' : 'fullscreen';
      });
    }

    // Window resize handler
    window.addEventListener('resize', function() {
      updateToggleIcon();
      if (window.innerWidth >= 992) {
        body.classList.remove('sidebar-mobile-open');
      }
    });

    // Contador de filtros activos en el botón "Filtros" de los reportes.
    // Cuenta los campos marcados con data-cuenta-filtro dentro de cada .filtros-sidebar
    // y lo muestra en el .filtros-badge del botón que abre ese panel.
    var actualizarBadgesFiltros = function() {
      document.querySelectorAll('.filtros-sidebar[id]').forEach(function(panel) {
        var activos = 0;
        panel.querySelectorAll('[data-cuenta-filtro]').forEach(function(campo) {
          var valor = window.jQuery ? window.jQuery(campo).val() : campo.value;
          if (Array.isArray(valor) ? valor.length > 0 : !!valor) activos++;
        });
        document.querySelectorAll('[data-bs-target="#' + panel.id + '"] .filtros-badge').forEach(function(badge) {
          badge.textContent = activos;
          badge.classList.toggle('d-none', activos === 0);
        });
      });
    };
    // Chips de filtros aplicados, alineados a la izquierda en la .report-actions-bar.
    // Si el panel tiene un botón [data-filtros-aplicar] (Consultar), los chips se refrescan
    // al consultar o limpiar; si no lo tiene (filtros instantáneos), se refrescan en vivo.
    var textoEtiqueta = function(panel, campo) {
      var label = campo.id ? panel.querySelector('label[for="' + campo.id + '"]') : null;
      if (!label) return campo.id || 'Filtro';
      var copia = label.cloneNode(true);
      copia.querySelectorAll('small').forEach(function(s) { s.remove(); });
      return copia.textContent.replace(/^\s*\d+\.\s*/, '').trim();
    };

    var renderChipsFiltros = function(panel) {
      var boton = document.querySelector('.report-actions-bar [data-bs-target="#' + panel.id + '"]');
      if (!boton) return;
      var barra = boton.closest('.report-actions-bar');
      var cont = barra.querySelector('.filtros-aplicados');
      if (!cont) {
        cont = document.createElement('div');
        cont.className = 'filtros-aplicados me-auto';
        barra.insertBefore(cont, barra.firstChild);
      }
      cont.innerHTML = '';
      panel.querySelectorAll('[data-cuenta-filtro]').forEach(function(campo) {
        var textos = Array.prototype.filter.call(campo.options || [], function(o) { return o.selected && o.value !== ''; })
          .map(function(o) { return o.text.trim(); });
        if (!textos.length) return;
        var etiqueta = textoEtiqueta(panel, campo);
        var visibles = textos.slice(0, 2).join(', ') + (textos.length > 2 ? ' +' + (textos.length - 2) : '');
        var chip = document.createElement('span');
        chip.className = 'filtro-chip';
        chip.title = etiqueta + ': ' + textos.join(', ');
        var b = document.createElement('b');
        b.textContent = etiqueta + ':';
        chip.appendChild(b);
        chip.appendChild(document.createTextNode(' ' + visibles));
        var quitar = document.createElement('button');
        quitar.type = 'button';
        quitar.className = 'filtro-chip-quitar';
        quitar.setAttribute('aria-label', 'Quitar filtro ' + etiqueta);
        quitar.innerHTML = '<span class="material-icons">close</span>';
        quitar.addEventListener('click', function() {
          if (window.jQuery) {
            window.jQuery(campo).val(null).trigger('change');
          } else {
            Array.prototype.forEach.call(campo.options, function(o) { o.selected = false; });
            campo.dispatchEvent(new Event('change', { bubbles: true }));
          }
          var aplicar = panel.querySelector('[data-filtros-aplicar]');
          if (aplicar) aplicar.click(); else renderChipsFiltros(panel);
          actualizarBadgesFiltros();
        });
        chip.appendChild(quitar);
        cont.appendChild(chip);
      });
    };

    var renderTodosLosChips = function() {
      document.querySelectorAll('.filtros-sidebar[id]').forEach(renderChipsFiltros);
    };

    if (document.querySelector('.filtros-sidebar')) {
      var alCambiar = function(e) {
        actualizarBadgesFiltros();
        var panel = e && e.target && e.target.closest ? e.target.closest('.filtros-sidebar') : null;
        if (panel && !panel.querySelector('[data-filtros-aplicar]')) renderChipsFiltros(panel);
      };
      if (window.jQuery) {
        // Select2 dispara "change" vía jQuery, no como evento nativo
        window.jQuery(document).on('change', '.filtros-sidebar [data-cuenta-filtro]', alCambiar);
      }
      document.addEventListener('change', alCambiar);
      // Consultar / Limpiar: refrescar badge y chips (Limpiar puede cambiar valores sin "change")
      document.addEventListener('click', function(e) {
        var footer = e.target.closest('.filtros-sidebar .filtros-footer');
        if (footer) {
          var panel = footer.closest('.filtros-sidebar');
          setTimeout(function() { actualizarBadgesFiltros(); renderChipsFiltros(panel); }, 50);
        }
      });
      setTimeout(function() { actualizarBadgesFiltros(); renderTodosLosChips(); }, 300);
    }

    // Universal Toggle for Report Banner Expandable Filters
    document.addEventListener('click', function(e) {
      var btn = e.target.closest('[data-action="toggle-banner-filters"]');
      if (!btn) return;
      e.preventDefault();

      var targetSelector = btn.getAttribute('data-target') || (btn.dataset && btn.dataset.target);
      var wrapper = targetSelector ? document.querySelector(targetSelector) : (btn.closest('.report-banner-card') ? btn.closest('.report-banner-card').querySelector('.report-filters-wrapper') : null);
      if (!wrapper) return;

      var isExpanded = wrapper.classList.toggle('is-expanded');
      wrapper.classList.toggle('is-collapsed', !isExpanded);
      btn.classList.toggle('active', isExpanded);

      var textEl = btn.querySelector('.toggle-text');
      var iconEl = btn.querySelector('.toggle-icon');
      if (textEl) {
        textEl.textContent = isExpanded ? 'Menos filtros' : 'Más filtros';
      }
      if (iconEl) {
        iconEl.textContent = isExpanded ? 'expand_less' : 'expand_more';
      }

      setTimeout(function() {
        window.dispatchEvent(new Event('resize'));
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.dataTable) {
          try {
            window.jQuery.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
          } catch (err) {}
        }
      }, 320);
    });

    updateToggleIcon();
  });
})();

