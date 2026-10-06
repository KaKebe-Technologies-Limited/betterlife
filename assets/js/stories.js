/* Stories: after subscribing, the newsletter form returns to this page; bring its message into view. */
(function () {
  'use strict';
  var flash = document.querySelector('.st-flash');
  if (flash && flash.scrollIntoView) flash.scrollIntoView({ block: 'center' });
})();
