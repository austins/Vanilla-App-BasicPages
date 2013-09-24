/**
 * Basic Pages - An application for Garden & Vanilla Forums.
 * Copyright (C) 2013  Livid Tech
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

jQuery(document).ready(function($) {
   // Map plain text page name to url code.
   $("#Form_Name").keyup(function(event) {
      if ($('#Form_UrlCodeIsDefined').val() == '0') {
         $('#UrlCode').show();
         var val = $(this).val().replace(/[ \/\\&.?;,<>'"]+/g, '-')
         val = val.replace(/\-+/g, '-').toLowerCase();
         $("#Form_UrlCode").val(val);
         $("#UrlCode span").text(val);
      }
   });
   // Make sure not to override any values set by the user.
   $('#UrlCode span').text($('#UrlCode input').val());
   $("#Form_UrlCode").focus(function() {
      $('#Form_UrlCodeIsDefined').val('1')
   });
   $('#UrlCode input, #UrlCode a.Save').hide();
   
   // Reveal input when "change" button is clicked.
   $('#UrlCode a, #UrlCode span').click(function() {
      $('#UrlCode').find('input,span,a').toggle();
      $('#UrlCode span').text($('#UrlCode input').val());
      $('#UrlCode input').focus();
      return false;
   });
   
   // Attach autogrow function to page body textarea.
   if($.autogrow) {
      $('textarea.TextBox').livequery(function() {
         $(this).autogrow();
      });
   }
});
