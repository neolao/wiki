var html = $('html');
var body = $('body');
var toolbar = $(document.createElement('div'));
toolbar.attr('id', 'toolbar');
body.before(toolbar);


// Animation
var toolbarHovered = false;
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
var onToolbarOver = function()
{
    toolbarHovered = true;
};
var onToolbarOut = function()
{
    toolbarHovered = false;
};
var onMouseMove = function(event)
{
    if (toolbarMoving || toolbarHovered) {
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
toolbar.hover(onToolbarOver, onToolbarOut);



// Table of contents
var toc = $(document.createElement('div'));
toc.attr('id', 'toc');
toolbar.append(toc);
$('h1, h2, h3, h4, h5, h6').each(function(index)
{
    var title = $(this);
    var text = title.text();
    var level = 0;
    switch (this.localName) {
        case 'h1':
            level = 1;
            break;
        case 'h2':
            level = 2;
            break;
        case 'h3':
            level = 3;
            break;
        case 'h4':
            level = 4;
            break;
        case 'h5':
            level = 5;
            break;
        case 'h6':
            level = 6;
            break;
    }

    var line = $(document.createElement('p'));
    line.addClass('level'+level);
    toc.append(line);

    var link = $(document.createElement('a'));
    link.attr('href', 'javascript:void(0)');
    link.text(text);
    line.append(link);
    link.click(function()
    {
        var scrollY = title.position().top - toc.position().top;
        $(document).scrollTop(scrollY);
    });
});