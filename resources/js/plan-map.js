/**
 * Dambulla plan: interactive SVG + owner grid + pagination (mirrors React App.tsx / InteractiveMap.tsx).
 */
const OWNERS_PER_PAGE = 27;
const TOTAL_PAGES = 3;

/** Matches App.tsx allOwners: page 1 → Owner 1–27, page 2 → 28–54, page 3 → 55–81 */
function ownerLabel(page, slotIndex) {
  const start = { 1: 1, 2: 28, 3: 55 }[page];
  return `Owner ${start + slotIndex}`;
}

function initPlanMap() {
  const root = document.querySelector('[data-plan-root]');
  const mount = document.querySelector('[data-map-mount]');
  const viewport = document.querySelector('[data-map-viewport]');
  const mapContent = document.querySelector('[data-map-content]');
  const zoomInBtn = document.querySelector('[data-zoom-in]');
  const zoomOutBtn = document.querySelector('[data-zoom-out]');
  const zoomResetBtn = document.querySelector('[data-zoom-reset]');
  const grid = document.querySelector('[data-owner-grid]');
  const prevBtn = document.querySelector('[data-page-prev]');
  const nextBtn = document.querySelector('[data-page-next]');
  const pageButtons = document.querySelectorAll('[data-page-num]');

  if (!root || !mount || !grid || !prevBtn || !nextBtn) return;

  const svgUrl = root.getAttribute('data-svg-url');
  if (!svgUrl) return;

  let currentPage = 1;
  let selectedOwner = null;
  let plotElements = [];
  let zoom = 1;
  let baseWidth = null;
  let baseHeight = null;
  let resizeObserver = null;

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
    for (let i = 0; i < OWNERS_PER_PAGE; i += 1) {
      const g = globalIndexForSlot(i);
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = ownerLabel(currentPage, i);
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

  function updatePaginationUi() {
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === TOTAL_PAGES;

    pageButtons.forEach((btn) => {
      const p = parseInt(btn.getAttribute('data-page-num'), 10);
      const active = p === currentPage;
      btn.className = active
        ? 'w-10 h-10 rounded-full font-medium transition-colors bg-gray-900 text-white'
        : 'w-10 h-10 rounded-full font-medium transition-colors text-gray-900 hover:bg-gray-100';
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
    plotElements.forEach((plot, index) => {
      plot.style.cursor = 'pointer';
      plot.style.transition = 'all 0.3s ease';
      plot.addEventListener('click', () => {
        selectedOwner = index;
        renderGrid();
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
    updatePaginationUi();
  });

  nextBtn.addEventListener('click', () => {
    currentPage = Math.min(TOTAL_PAGES, currentPage + 1);
    renderGrid();
    updatePaginationUi();
  });

  pageButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      const p = parseInt(btn.getAttribute('data-page-num'), 10);
      if (p >= 1 && p <= TOTAL_PAGES) {
        currentPage = p;
        renderGrid();
        updatePaginationUi();
      }
    });
  });

  fetch(svgUrl)
    .then((r) => r.text())
    .then((html) => {
      mount.innerHTML = html;
      const svg = mount.querySelector('svg');
      if (svg) wireSvg(svg);
    })
    .catch(() => {
      mount.innerHTML =
        '<p class="text-gray-500 text-center p-8">Could not load the plan map. Check that the SVG exists in public/images.</p>';
    });

  renderGrid();
  updatePaginationUi();

  // Zoom controls (only affects mapContent / map panel)
  if (zoomInBtn) zoomInBtn.addEventListener('click', () => setZoom(zoom + 0.1));
  if (zoomOutBtn) zoomOutBtn.addEventListener('click', () => setZoom(zoom - 0.1));
  if (zoomResetBtn) zoomResetBtn.addEventListener('click', () => setZoom(1));

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
    });
    resizeObserver.observe(viewport);
  } else {
    window.addEventListener('resize', () => {
      measureBaseSize();
      applyZoom();
    });
  }

  applyZoom();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initPlanMap);
} else {
  initPlanMap();
}
