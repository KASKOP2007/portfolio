jQuery(function($) {

    const STRINGS = {
        NONE_SELECTED: 'Selectie uitgevinkt',
        ALL_SELECTED: 'Alles is nu geselecteerd'
    };

    function init() {
        // toggle the domain
        $('.js-domain-title').on('dblclick', event => {
            event.preventDefault();
            CheckboxCollection.fromTitle(event.target).toggleAll();
        });
        // toggle all
        $('.js-all').click(event => {
            event.preventDefault();
            const result = (new CheckboxCollection).toggleAll();
            (new InlineNotification({
                text: result.checked ? STRINGS.ALL_SELECTED : STRINGS.NONE_SELECTED,
                time: 5,
                after: event.target,
                single: event.target,
            })).show();
        });
        (new CheckboxOperationsService(new CheckboxCollection))
            .allowRangeToggle({firstActive: true});
    }

    init();

});
