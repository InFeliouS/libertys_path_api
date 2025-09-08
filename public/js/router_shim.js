// public/js/router_shim.js
// Rewrites any accidental absolute links (/sections/...) to router paths.
document.addEventListener('click', function(e){
  // Anchor clicks
  const a = e.target.closest('a[href]');
  if (!a) return;
  const href = a.getAttribute('href');
  if (!href) return;
  if (href.startsWith('/sections/view')) {
    e.preventDefault();
    const url = new URL(href, window.location.origin);
    const id = url.searchParams.get('section_id') || '';
    window.location.href = `index.php?r=sections/detail&section_id=${id}`;
  } else if (href === '/sections/create') {
    e.preventDefault();
    window.location.href = 'index.php?r=sections/create';
  } else if (href === '/register') {
    e.preventDefault();
    window.location.href = 'index.php?r=register';
  } else if (href === '/dashboard') {
    e.preventDefault();
    window.location.href = 'index.php?r=dashboard';
  }
}, true);

// Also rewrite any JS-triggered redirects using window.location.pathname at runtime.
(function(){
  const origAssign = window.location.assign.bind(window.location);
  window.location.assign = function(url){
    try {
      if (typeof url === 'string' && url.startsWith('/sections/view')) {
        const u = new URL(url, window.location.origin);
        const id = u.searchParams.get('section_id') || '';
        return origAssign(`index.php?r=sections/detail&section_id=${id}`);
      }
    } catch (e) {}
    return origAssign(url);
  };
})();
