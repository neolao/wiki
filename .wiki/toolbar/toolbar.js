var html = $('html');
var body = $('body');
var toolbar = $(document.createElement('div'));
toolbar.attr('id', 'toolbar');
body.before(toolbar);


// Animation
var toolbarOpened = true;
var toolbarMoving = false;
var onToolbarOpened = function()
{
    toolbarOpened = true;
    toolbarMoving = false;
};
var onToolbarClosed = function()
{
    toolbarOpened = false;
    toolbarMoving = false;
};
var onMouseMove = function(event)
{
    if (toolbarMoving) {
        return;
    }

    var toolbarY = toolbar.position().top;
    var mouseX = event.pageX;
    var mouseY = event.pageY - toolbarY;
    var height = toolbar.height() + 20;

    if (toolbarOpened && mouseY > height) {
        toolbarMoving = true;
        toolbar.fadeOut(200, onToolbarClosed);
    } else if (!toolbarOpened && mouseY <= height) {
        toolbarMoving = true;
        toolbar.fadeIn(200, onToolbarOpened);
    }
};
var onMouseLeave = function(event)
{
    toolbarMoving = true;
    toolbar.fadeOut(200, onToolbarClosed);
};
html.mousemove(onMouseMove);
html.mouseleave(onMouseLeave);

