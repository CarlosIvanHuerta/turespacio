/* global $ */
$(function mgGallery() {
   $.fn.mgGallery = function () {
      var gallery = $(this)
      var inner = gallery.find('.mg-inner')

      var navigation = gallery.find('[data-navigation]')
      navigation.click(function (ev) {
         var dir = this.dataset.navigation
         var firstChild = inner.find('li:first-child')
         var lastChild = inner.find('li:last-child')

         if (dir === 'left') lastChild.after(firstChild)
         else if (dir === 'right') firstChild.before(lastChild)
      })
   }
})

$(function () {
   $('.mg-body').mgGallery()
})