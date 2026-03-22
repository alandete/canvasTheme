/* ============================================
   Canvas Themes – Documentación
   TOC active state on scroll
   ============================================ */

(function () {
  'use strict';

  var tocLinks = document.querySelectorAll('.toc-link');
  var sections = document.querySelectorAll('section[id]');

  // Scroll spy
  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        tocLinks.forEach(function (link) { link.classList.remove('active'); });
        var active = document.querySelector('.toc-link[href="#' + entry.target.id + '"]');
        if (active) active.classList.add('active');
      }
    });
  }, {
    rootMargin: '-80px 0px -60% 0px',
    threshold: 0
  });

  sections.forEach(function (section) {
    observer.observe(section);
  });

  // Smooth scroll on click
  tocLinks.forEach(function (link) {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      var targetId = link.getAttribute('href').substring(1);
      var target = document.getElementById(targetId);
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
      tocLinks.forEach(function (l) { l.classList.remove('active'); });
      link.classList.add('active');
    });
  });
})();
