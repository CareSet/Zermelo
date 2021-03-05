/*
    This code is mainly from: https://datatables.net/forums/discussion/34372/how-to-remove-fixed-column-after-initializing-in-jquery
    with the exception of delete this.s.dt._oFixedColumns;
*/
$.fn.dataTable.FixedColumns.prototype.destroy = function(){
    var nodes = ['body', 'footer', 'header'];
 
    //remove the cloned nodes
    for(var i = 0, l = nodes.length; i < l; i++){
        if(this.dom.clone.left[nodes[i]]){
            this.dom.clone.left[nodes[i]].parentNode.removeChild(this.dom.clone.left[nodes[i]]);
        }
        if(this.dom.clone.right[nodes[i]]){
            this.dom.clone.right[nodes[i]].parentNode.removeChild(this.dom.clone.right[nodes[i]]);
        }
    }
 
    //remove event handlers
    $(this.s.dt.nTable).off( 'column-sizing.dt.DTFC destroy.dt.DTFC draw.dt.DTFC' );
 
    $(this.dom.scroller).off( 'scroll.DTFC mouseover.DTFC' );
    $(window).off( 'resize.DTFC' );
 
    $(this.dom.grid.left.liner).off( 'scroll.DTFC wheel.DTFC mouseover.DTFC' );
    $(this.dom.grid.left.wrapper).remove();
 
    $(this.dom.grid.right.liner).off( 'scroll.DTFC wheel.DTFC mouseover.DTFC' );
    $(this.dom.grid.right.wrapper).remove();
 
    $(this.dom.body).off('mousedown.FC mouseup.FC mouseover.FC click.FC');
 
    //remove DOM elements
    var $scroller = $(this.dom.scroller).parent();
    var $wrapper = $(this.dom.scroller).closest('.DTFC_ScrollWrapper');
    $scroller.insertBefore($wrapper);
    $wrapper.remove();
 
	//cleanup variables for GC
	delete this.s.dt._oFixedColumns;
    delete this.s;
	delete this.dom;
};
