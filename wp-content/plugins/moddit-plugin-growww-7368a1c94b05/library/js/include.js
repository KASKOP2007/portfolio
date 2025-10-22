jQuery(function($) {

    /**
     * Represents a collection of checkboxes, either one domain or ALL checkboxes
     */
     class CheckboxCollection
     {
         parent
         
         /**
          * @param {DOMElement|jQuery} parent   provide the domain element, or an element within, to create a domain instance
          *                                     leave empty to create a global instance for all checkboxes
          */
         constructor(parent)
         {
             if (parent) {
                 this.parent = $(parent).closest('.js-domain-strings');
             } else {
                 this.parent = $('.js-scan-results, .js-scan-locations');
             }
         }
 
         /**
          * Get all checkboxes, or all which are/are not checked
          * @param {bool} filterChecked   filters checkboxes by checked state, if provided
          * @returns {jQuery}
          */
         getCheckboxes(filterChecked)
         {
             let all = this.parent.find('[type=checkbox]');
             if (filterChecked !== undefined) {
                 all = filterChecked ? all.filter(':checked') : all.not(':checked');
             }
             return all;
         }
 
         /**
          * Unchecks all if some checked, or checks all if none checked
          * @returns {Object}
          *                      {
          *                      bool    checked     Did we check? (as opposed to uncheck)
          *                      jQuery  checkboxes  Collection of checkboxes
          *                      }
          */
         toggleAll()
         {
             const checked = !this.getSomeChecked();
             const checkboxes = this.getCheckboxes(!checked);
             if (!checkboxes.length) {
                 return null;
             }
             checkboxes.each((index, element) => {
                 $(element).prop('checked', checked);
             });
             return {checked, checkboxes};
         }
 
         /**
          * are some checkboxes checked?
          */
         getSomeChecked()
         {
             return this.getCheckboxes(true).length > 0;
         }
 
         addEvent(eventType, callback)
         {
             const selector = '[type=checkbox],label';
             this.parent.on(eventType, selector, callback);
             return () => {
                 this.parent.off(eventType, selector, callback);
             };
         }
 
         /**
          * Get an instance belonging to a domain title element
          * @param {DOMElement|jQuery} element
          */
         static fromTitle(element)
         {
             const container = $(element).nextAll('.js-domain-strings').eq(0);
             return new this(container);
         }
     }
 
 
     /**
      * Perform operations on a checkbox/set of checkboxes
      */
     class CheckboxOperationsService
     {
         /** @var {jQuery} Last clicked checkbox */
         lastElement
         /** @var {bool} After using shift click, to keep the highlight class from being added to the current checkbox */
         toggled
         /** @var {CheckboxCollection} */
         collection
 
         constructor(collection)
         {
             this.collection = collection;
         }
 
 
         /**
          * add Range Toggle to this group of checkboxes: shift-click a box to toggle all boxes up to the last clicked box
          * @returns this
          */
         allowRangeToggle(options = {})
         {
             this.collection.addEvent('click', event => {
                 if (event.target !== event.currentTarget) {
                     return;
                 }
                 const shift = !!event.originalEvent?.shiftKey;
                 let checkbox = $(event.target);
                 const clickedLabel = checkbox.prop('tagName').toLowerCase() === 'label';
                 if (clickedLabel) {
                     checkbox = checkbox.children('input');
                 }
                 if (shift && this.lastElement) {
                     clearSelection();
                     const lastElement = this.lastElement;
                     const setChecked = clickedLabel ? !checkbox.prop('checked') : checkbox.prop('checked');
                     setTimeout(() => {
                         const all = this.collection.getCheckboxes();
                         const index1 = all.index(lastElement);
                         const index2 = all.index(checkbox);
                         const step = index2 > index1 ? 1 : -1;
                         for (let index = index1; index !== index2 + step; index += step) {
                             $(all[index]).prop('checked', setChecked);
                         }
                         lastElement.removeClass('highlight');
                     }, 0);
                     this.toggled = true;
                 }
                 this.lastElement = checkbox;
             });
             $(window).on('keyup keydown', event => {
                 if (event.originalEvent?.key === 'Shift' && this.lastElement) {
                     const down = event.type === 'keydown';
                     if (this.toggled) {
                         if (down) {
                             return;
                         }
                         this.toggled = false;
                     }
                     this.lastElement.toggleClass('highlight', down);
                 }
             });
             if (options.firstActive) {
                 this.lastElement = this.collection.getCheckboxes().eq(0);
             }
         }
     }
 
     
    class InlineNotification
    {
        options
        
        constructor(options)
        {
            this.options = options;
            this.element = $('<div>').addClass('information');
            this.element.text(this.options.text);
            if (this.options.after) {
                this.element.insertAfter(this.options.after);
            }
            this.element.slideUp(0);
            if (this.options.single) {
                const boundElement = $(this.options.single);
                if (boundElement.data('inlineNotification')) {
                    boundElement.data('inlineNotification').remove();
                    this.element.slideDown(0);
                }
                boundElement.data('inlineNotification', this);
            }
        }

        show()
        {
            this.element.slideDown();
            if (this.options.time) {
                this.element.delay(this.options.time * 1000).slideUp(() => this.remove());
            }
        }

        remove()
        {
            this.element.remove();
            if (this.options.single) {
                $(this.options.single).removeData('inlineNotification');
            }
        }
    }


    function clearSelection()
    {
        if (window.getSelection) {
            if (window.getSelection().empty) {  // Chrome
                window.getSelection().empty();
            } else if (window.getSelection().removeAllRanges) {  // Firefox
                window.getSelection().removeAllRanges();
            }
        } else if (document.selection) {  // IE?
            document.selection.empty();
        }
    }


    $.extend(window, {
        CheckboxCollection,
        CheckboxOperationsService,
        InlineNotification,
        clearSelection,
    });
});
