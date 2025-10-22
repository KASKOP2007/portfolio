jQuery(function($) {

    const FLAGS = {
        NOTIFIED_DOUBLE_CLICK: false
    };
    const STRINGS = {
        CONFIRM_DOUBLE_CLICK: 'Met een dubbelklik op de String ID wordt de standaardwaarde bij dit veld ingevuld! Doorgaan?\n(Deze melding wordt 1x getoond)',
        CONFIRM_ALL_DEFAULT: 'Hiermee worden alle waarden op de standaard waarde gezet! Doorgaan?',
        CONFIRM_ALL_EMPTY: 'Hiermee worden alle waarden leeggemaakt! Doorgaan?'
    };

    function init() {
        $('.js-translations-table').on('dblclick', '.js-msgid', event => {
            const row = TransRow.fromElement(event.target);
            if (row.getValue() !== row.getDefault()) {
                if (!FLAGS.NOTIFIED_DOUBLE_CLICK && row.getValue() !== '') {
                    FLAGS.NOTIFIED_DOUBLE_CLICK = true;
                    if (!confirm(STRINGS.CONFIRM_DOUBLE_CLICK)) {
                        return;
                    }
                }
                row.setValue(row.getDefault());
            }
            row.getInput().focus();
        });
        $('.js-all-default').click(event => {
            event.preventDefault();
            if (TransRow.areAllEmptyOrDefault() || confirm(STRINGS.CONFIRM_ALL_DEFAULT)) {
                TransRow.forEach(row => row.setValue(row.getDefault()));
            }
        });
        $('.js-all-empty').click(event => {
            event.preventDefault();
            if (!TransRow.areAllEmpty() && confirm(STRINGS.CONFIRM_ALL_EMPTY)) {
                TransRow.forEach(row => row.setValue(''));
            }
        });
    }

    /**
     * Wrapper for one string/row
     */
    class TransRow
    {
        element
        input
        
        constructor(element)
        {
            this.element = $(element).closest('tr');
            this.element.data('row', this);
        }

        /**
         * Get default value
         */
        getDefault()
        {
            return this.getInput().attr('placeholder');
        }

        getValue()
        {
            return this.getInput().val();
        }

        setValue(value)
        {
            return this.getInput().val(value);
        }

        getInput()
        {
            return this.input || ( this.input = this.element.find('input') );
        }

        static fromElement(element)
        {
            element = $(element).closest('tr');
            return element.data('row') || new this(element);
        }

        static forEach(callback)
        {
            $('.js-translations-table tr').slice(1).each((index, element) => {
                const row = this.fromElement(element);
                callback.call(row, row, index);
            });
        }

        static checkAny(callback)
        {
            const collection = $('.js-translations-table tr').slice(1);
            for (let index = 0; index < collection.length; index++) {
                const row = this.fromElement(collection[index]);
                const returnValue = callback.call(row, row, index);
                if (returnValue) {
                    return true;
                }
            }
            return false;
        }

        static areAllEmpty()
        {
            return !this.checkAny(row => row.getValue() !== '');
        }

        static areAllEmptyOrDefault()
        {
            return !this.checkAny(row => row.getValue() !== '' && row.getValue() !== row.getDefault());
        }
    }

    init();

});
