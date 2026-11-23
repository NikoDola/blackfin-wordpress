(() => {
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const header = document.querySelector('.bf-nav-header');
  const toggle = header?.querySelector('[data-menu-toggle]');
  const menu = header?.querySelector('.bf-nav-desktopNav');
  const synchronizeHeader = () => { if (header) header.dataset.scrolled = String(window.scrollY > 20); };

  if (header) {
    synchronizeHeader();
    window.addEventListener('scroll', synchronizeHeader, { passive: true });
  }

  if (toggle && menu) {
    const mobile = window.matchMedia('(max-width: 767px)');
    menu.id ||= 'blackfin-primary-nav';
    toggle.setAttribute('aria-controls', menu.id);
    const setOpen = (open) => {
      header.dataset.menuOpen = String(open);
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    };
    setOpen(false);
    toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
    menu.addEventListener('click', event => { if (event.target.closest('a')) setOpen(false); });
    header.addEventListener('keydown', event => {
      if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
        setOpen(false);
        toggle.focus();
      }
    });
    mobile.addEventListener('change', () => setOpen(false));
  }

  for (const hero of document.querySelectorAll('.bf-hero-section')) {
    const slides = Array.from(hero.querySelectorAll('.bf-hero-slide'));
    const dots = Array.from(hero.querySelectorAll('[data-slide]'));
    const pause = hero.querySelector('[data-carousel-pause]');
    let current = 0;
    let timeout;
    let paused = false;

    if (slides.length < 2 || dots.length !== slides.length) continue;

    function schedule() {
      window.clearTimeout(timeout);
      if (paused || reducedMotion.matches || document.hidden || hero.matches(':focus-within, :hover')) return;
      timeout = window.setTimeout(() => show(current + 1), 10000);
    }

    function show(index) {
      current = (index + slides.length) % slides.length;
      slides.forEach((slide, i) => { slide.hidden = i !== current; });
      dots.forEach((dot, i) => dot.setAttribute('aria-current', String(i === current)));
      schedule();
    }

    hero.dataset.ready = 'true';
    hero.querySelector('[data-carousel-prev]')?.addEventListener('click', () => show(current - 1));
    hero.querySelector('[data-carousel-next]')?.addEventListener('click', () => show(current + 1));
    dots.forEach((dot, i) => dot.addEventListener('click', () => show(i)));
    pause?.addEventListener('click', () => {
      paused = !paused;
      pause.setAttribute('aria-pressed', String(paused));
      pause.setAttribute('aria-label', paused ? 'Resume automatic slides' : 'Pause automatic slides');
      schedule();
    });
    ['mouseenter', 'mouseleave', 'focusin', 'focusout'].forEach(name => hero.addEventListener(name, () => setTimeout(schedule)));
    document.addEventListener('visibilitychange', schedule);
    reducedMotion.addEventListener('change', schedule);
    show(0);
  }

  const parallax = [...document.querySelectorAll('.bf-feature-imageParallax, .bf-hero-bgLayer')];
  let frame = 0;

  function draw() {
    frame = 0;
    for (const image of parallax) {
      const section = image.closest('.bf-feature-section, .bf-hero-section');
      const rect = section.getBoundingClientRect();
      if (reducedMotion.matches) {
        image.style.removeProperty('transform');
      } else if (rect.bottom >= 0 && rect.top <= innerHeight) {
        const hero = section.classList.contains('bf-hero-section');
        const offset = hero
          ? Math.min(1, Math.max(0, -rect.top / rect.height)) * 30
          : ((innerHeight - rect.top) / (innerHeight + rect.height)) * 16 - 8;
        image.style.transform = `translateY(${offset}%)`;
      }
    }
  }

  function scheduleParallax() {
    if (!frame) frame = window.requestAnimationFrame(draw);
  }

  if (parallax.length) {
    window.addEventListener('scroll', scheduleParallax, { passive: true });
    window.addEventListener('resize', scheduleParallax);
    reducedMotion.addEventListener('change', scheduleParallax);
    scheduleParallax();
  }

  if (!reducedMotion.matches && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => {
      for (const entry of entries) {
        if (!entry.isIntersecting) continue;
        entry.target.dataset.entered = 'true';
        observer.unobserve(entry.target);
      }
    });
    const elements = document.querySelectorAll(
      '.bf-feature-textWrap, .bf-services-card, .bf-gallery-item, .bf-quote-infoCard, .bf-quote-formPanel'
    );
    elements.forEach(element => observer.observe(element));
  }

  document.addEventListener('wpcf7statuschanged', event => {
    const form = event.target.closest('.bf-quote-formPanel')?.querySelector('form');
    const button = form?.querySelector('input[type="submit"]');
    if (!button) return;
    const pending = event.detail.status === 'submitting';
    if (pending) {
      button.dataset.idleLabel ||= button.value;
      button.value = 'Sending…';
    } else if (button.dataset.idleLabel) {
      button.value = button.dataset.idleLabel;
      delete button.dataset.idleLabel;
    }
    form.setAttribute('aria-busy', String(pending));
  });

  document.addEventListener('wpcf7mailsent', event => {
    const form = event.target.closest('.wpcf7');
    const result = form?.closest('.bf-quote-formPanel')?.querySelector('.bf-quote-success');
    if (result) {
      form.hidden = true;
      result.hidden = false;
      result.querySelector('button')?.focus();
    }
  });

  for (const button of document.querySelectorAll('[data-quote-reset]')) {
    button.addEventListener('click', () => {
      const result = button.closest('.bf-quote-success');
      const form = result.parentElement.querySelector('.wpcf7');
      if (!form) return;
      result.hidden = true;
      form.hidden = false;
      form.querySelector('input:not([type=hidden])')?.focus();
    });
  }
})();

