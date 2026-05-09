/**
 * Dambulla plan: interactive SVG + owner grid + pagination (mirrors React App.tsx / InteractiveMap.tsx).
 */
const OWNERS_PER_PAGE = 27;

function ownerLabel(globalIndex) {
  return `Owner ${globalIndex + 1}`;
}

function initPlanMap() {
  const root = document.querySelector('[data-plan-root]');
  const mount = document.querySelector('[data-map-mount]');
  const viewport = document.querySelector('[data-map-viewport]');
  const mapContent = document.querySelector('[data-map-content]');
  const zoomInBtn = document.querySelector('[data-zoom-in]');
  const zoomOutBtn = document.querySelector('[data-zoom-out]');
  const zoomResetBtn = document.querySelector('[data-zoom-reset]');
  const viewDetailsBtn = document.querySelector('[data-view-details]');
  const detailsModal = document.querySelector('[data-details-modal]');
  const detailsOverlay = document.querySelector('[data-details-overlay]');
  const detailsCloseBtns = document.querySelectorAll('[data-details-close]');
  const detailsSubtitle = document.querySelector('[data-details-subtitle]');
  const detailsBody = document.querySelector('[data-details-body]');
  const grid = document.querySelector('[data-owner-grid]');
  const prevBtn = document.querySelector('[data-page-prev]');
  const nextBtn = document.querySelector('[data-page-next]');
  const pageButtonsWrap = document.querySelector('[data-page-buttons]');

  if (!root || !mount || !grid || !prevBtn || !nextBtn || !pageButtonsWrap) return;

  const svgUrl = root.getAttribute('data-svg-url');
  if (!svgUrl) return;

  let currentPage = 1;
  let selectedOwner = null;
  let plotElements = [];
  let zoom = 1;
  let baseWidth = null;
  let baseHeight = null;
  let resizeObserver = null;
  let totalPages = 1;
  let totalPlots = 0;
  let pageButtons = [];

  function isMobilePagination() {
    return window.matchMedia && window.matchMedia('(max-width: 639px)').matches;
  }

  function pageButtonClass(active) {
    const base = isMobilePagination() ? 'w-8 h-8 text-sm' : 'w-10 h-10 text-base';
    return active
      ? `${base} rounded-full font-medium transition-colors bg-gray-900 text-white`
      : `${base} rounded-full font-medium transition-colors text-gray-900 hover:bg-gray-100`;
  }

  function openDetailsModal() {
    if (!detailsModal) return;
    detailsModal.classList.remove('hidden');
    detailsModal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
  }

  function closeDetailsModal() {
    if (!detailsModal) return;
    detailsModal.classList.add('hidden');
    detailsModal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
  }

  function setDetailsContent() {
    if (!detailsSubtitle || !detailsBody) return;

    if (selectedOwner == null) {
      detailsSubtitle.textContent = 'No selection';
      detailsBody.innerHTML =
        '<div>Select a plot on the map or an owner button, then tap “View details”.</div>';
      return;
    }

    const plotNumber = selectedOwner + 1;
    detailsSubtitle.textContent = `Selected plot ${plotNumber}`;

    const ownerText = selectedOwner < 81 ? `Owner ${plotNumber}` : `Owner (not available)`;
    detailsBody.innerHTML = [
      `<div><span class="font-semibold">Plot:</span> ${plotNumber}</div>`,
      `<div><span class="font-semibold">Owner:</span> ${ownerText}</div>`,
      `<div class="text-gray-600">Replace this with your real plot/owner data when available.</div>`,
    ].join('');
  }

  function clamp(n, min, max) {
    return Math.min(max, Math.max(min, n));
  }

  function applyZoom() {
    if (!mapContent) return;
    // Use real dimensions so the map panel can scroll (CSS transforms don't affect scroll size).
    if (baseWidth && baseHeight) {
      mapContent.style.width = `${Math.round(baseWidth * zoom)}px`;
      mapContent.style.height = `${Math.round(baseHeight * zoom)}px`;
    } else {
      mapContent.style.transform = `scale(${zoom})`;
    }
  }

  function measureBaseSize() {
    if (!viewport) return;
    baseWidth = viewport.clientWidth || 1;
    baseHeight = viewport.clientHeight || 1;
  }

  function setZoom(next) {
    zoom = clamp(next, 0.5, 3);
    applyZoom();
  }

  function globalIndexForSlot(slotIndex) {
    return (currentPage - 1) * OWNERS_PER_PAGE + slotIndex;
  }

  function renderGrid() {
    grid.innerHTML = '';
    const start = (currentPage - 1) * OWNERS_PER_PAGE;
    const end = Math.min(start + OWNERS_PER_PAGE, totalPlots);
    for (let g = start; g < end; g += 1) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = ownerLabel(g);
      btn.className =
        'flex items-center justify-center px-3 py-2 sm:px-6 sm:py-3 text-sm sm:text-base leading-none whitespace-nowrap border-2 rounded-lg transition-colors font-medium border-gray-900 bg-white hover:bg-gray-100 text-gray-900';
      if (selectedOwner === g) {
        btn.className =
          'flex items-center justify-center px-3 py-2 sm:px-6 sm:py-3 text-sm sm:text-base leading-none whitespace-nowrap border-2 rounded-lg transition-colors font-medium border-blue-600 bg-blue-50 text-blue-900';
      }
      btn.addEventListener('click', () => {
        selectedOwner = g;
        renderGrid();
        updatePaginationUi();
        applyPlotSelection();
      });
      grid.appendChild(btn);
    }
  }

  function renderPageButtons() {
    pageButtonsWrap.innerHTML = '';
    pageButtons = [];

    // Keep it compact on huge plans. Even more compact on mobile.
    const maxButtons = isMobilePagination() ? 5 : 7;
    let pagesToShow = [];
    if (totalPages <= maxButtons) {
      pagesToShow = Array.from({ length: totalPages }, (_, i) => i + 1);
    } else {
      const windowSize = isMobilePagination() ? 3 : 5;
      let start = Math.max(1, currentPage - Math.floor(windowSize / 2));
      let end = Math.min(totalPages, start + windowSize - 1);
      start = Math.max(1, end - windowSize + 1);
      pagesToShow = [1];
      if (start > 2) pagesToShow.push('…');
      for (let p = start; p <= end; p += 1) {
        if (p !== 1 && p !== totalPages) pagesToShow.push(p);
      }
      if (end < totalPages - 1) pagesToShow.push('…');
      pagesToShow.push(totalPages);
    }

    pagesToShow.forEach((p) => {
      if (p === '…') {
        const span = document.createElement('span');
        span.className = isMobilePagination()
          ? 'w-8 h-8 flex items-center justify-center text-gray-500'
          : 'w-10 h-10 flex items-center justify-center text-gray-500';
        span.textContent = '…';
        pageButtonsWrap.appendChild(span);
        return;
      }
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.setAttribute('data-page-num', String(p));
      btn.className = pageButtonClass(false);
      btn.textContent = String(p);
      btn.addEventListener('click', () => {
        currentPage = p;
        renderGrid();
        renderPageButtons();
        updatePaginationUi();
      });
      pageButtons.push(btn);
      pageButtonsWrap.appendChild(btn);
    });
  }

  function updatePaginationUi() {
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages;

    pageButtons.forEach((btn) => {
      const p = parseInt(btn.getAttribute('data-page-num'), 10);
      const active = p === currentPage;
      btn.className = pageButtonClass(active);
    });
  }

  function applyPlotSelection() {
    plotElements.forEach((el, index) => {
      if (selectedOwner === index) {
        el.style.filter = 'url(#drop-shadow)';
        el.style.transform = 'scale(1.05)';
        el.style.transformOrigin = 'center';
      } else {
        // InteractiveMap.tsx only clears filter + transform on deselect
        el.style.filter = '';
        el.style.transform = '';
      }
    });
  }

  function wireSvg(svgElement) {
    svgElement.setAttribute('width', '100%');
    svgElement.setAttribute('height', '100%');
    svgElement.style.display = 'block';

    const defs = svgElement.querySelector('defs');
    if (defs && !svgElement.querySelector('#drop-shadow')) {
      const filter = document.createElementNS('http://www.w3.org/2000/svg', 'filter');
      filter.setAttribute('id', 'drop-shadow');
      const feDropShadow = document.createElementNS('http://www.w3.org/2000/svg', 'feDropShadow');
      feDropShadow.setAttribute('dx', '0');
      feDropShadow.setAttribute('dy', '4');
      feDropShadow.setAttribute('stdDeviation', '8');
      feDropShadow.setAttribute('flood-opacity', '0.3');
      filter.appendChild(feDropShadow);
      defs.appendChild(filter);
    }

    plotElements = Array.from(svgElement.querySelectorAll('.st0, .st1'));
    totalPlots = plotElements.length;
    totalPages = Math.max(1, Math.ceil(totalPlots / OWNERS_PER_PAGE));
    plotElements.forEach((plot, index) => {
      plot.style.cursor = 'pointer';
      plot.style.transition = 'all 0.3s ease';
      plot.addEventListener('click', () => {
        selectedOwner = index;
        currentPage = Math.floor(index / OWNERS_PER_PAGE) + 1;
        renderGrid();
        renderPageButtons();
        updatePaginationUi();
        applyPlotSelection();
      });
    });

    if (viewport && mapContent) {
      measureBaseSize();
      mapContent.style.width = `${baseWidth}px`;
      mapContent.style.height = `${baseHeight}px`;
    }

    applyPlotSelection();
  }

  prevBtn.addEventListener('click', () => {
    currentPage = Math.max(1, currentPage - 1);
    renderGrid();
    renderPageButtons();
    updatePaginationUi();
  });

  nextBtn.addEventListener('click', () => {
    currentPage = Math.min(totalPages, currentPage + 1);
    renderGrid();
    renderPageButtons();
    updatePaginationUi();
  });

  fetch(svgUrl)
    .then((r) => r.text())
    .then((html) => {
      mount.innerHTML = html;
      const svg = mount.querySelector('svg');
      if (svg) {
        wireSvg(svg);
        renderGrid();
        renderPageButtons();
        updatePaginationUi();
      }
    })
    .catch(() => {
      mount.innerHTML =
        '<p class="text-gray-500 text-center p-8">Could not load the plan map. Check that the SVG exists in public/images.</p>';
    });

  // Zoom controls (only affects mapContent / map panel)
  if (zoomInBtn) zoomInBtn.addEventListener('click', () => setZoom(zoom + 0.1));
  if (zoomOutBtn) zoomOutBtn.addEventListener('click', () => setZoom(zoom - 0.1));
  if (zoomResetBtn) zoomResetBtn.addEventListener('click', () => setZoom(1));

  // Details modal
  if (viewDetailsBtn) {
    viewDetailsBtn.addEventListener('click', () => {
      setDetailsContent();
      openDetailsModal();
    });
  }
  if (detailsOverlay) detailsOverlay.addEventListener('click', closeDetailsModal);
  detailsCloseBtns.forEach((btn) => btn.addEventListener('click', closeDetailsModal));
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeDetailsModal();
  });

  if (viewport) {
    viewport.addEventListener(
      'wheel',
      (e) => {
        // Only zoom the map panel, and keep normal page scroll elsewhere.
        e.preventDefault();
        const delta = e.deltaY;
        const step = delta > 0 ? -0.08 : 0.08;
        setZoom(zoom + step);
      },
      { passive: false }
    );
  }

  // Keep map sizing correct across device rotations / resizes.
  if (viewport && mapContent && 'ResizeObserver' in window) {
    resizeObserver = new ResizeObserver(() => {
      const prevW = baseWidth;
      const prevH = baseHeight;
      measureBaseSize();

      // Maintain the same zoom level, just recompute physical size.
      if (prevW !== baseWidth || prevH !== baseHeight) applyZoom();

      // Pagination may need to re-render at breakpoints.
      renderPageButtons();
      updatePaginationUi();
    });
    resizeObserver.observe(viewport);
  } else {
    window.addEventListener('resize', () => {
      measureBaseSize();
      applyZoom();
      renderPageButtons();
      updatePaginationUi();
    });
  }

  applyZoom();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initPlanMap);
} else {
  initPlanMap();
}
