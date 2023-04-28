const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { createElement } = wp.element;

registerBlockType( 'vkard/vkard-block', {
    title: __( 'vKard Widget' ),
    description: __( 'Add a vKard widget to your page.' ),
    icon: 'id',
    category: 'widgets',

    edit: function( props ) {
        return createElement(
            'div',
            null,
            __( 'vKard Widget' )
        );
    },

    save: function() {
        return null; // The block will be rendered on the server-side
    }
});

