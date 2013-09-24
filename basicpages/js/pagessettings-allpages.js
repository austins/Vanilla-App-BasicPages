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
   if ($.ui && $.ui.nestedSortable) {
      $('ol.Sortable').nestedSortable({
         disableNesting: 'NoNesting',
         errorClass: 'SortableError',
         forcePlaceholderSize: true,
         handle: 'div',
         items: 'li',
         maxLevels: 1,
         opacity: .6,
         placeholder: 'Placeholder',
         tolerance: 'pointer',
         toleranceElement: '> div',
         update: function() {
            $.post(
                    gdn.url('/pagessettings/sortpages.json'),
                    {
                       'TreeArray': $('ol.Sortable').nestedSortable('toArray', {startDepthCount: 0}),
                       'TransientKey': gdn.definition('TransientKey')
                    },
               function(response) {
                  if(!response || !response.Result) {
                     alert("Error: didn't save order properly.");
                  }
               }
            );
         }
      });
   }
});
