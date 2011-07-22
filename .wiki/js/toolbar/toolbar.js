var html = $('html');
var body = $('body');
var toolbar = $(document.createElement('div'));
toolbar.attr('id', 'toolbar');
body.before(toolbar);


// Animation
var toolbarHovered = false;
var toolbarOpened = false;
var toolbarMoving = false;
var toolbarTimeoutId;
var onToolbarOpened = function()
{
    toolbarOpened = true;
    toolbarMoving = false;
};
var onToolbarClosed = function()
{
    toolbarOpened = false;
    toolbarMoving = false;
    toolbarHovered = false;
};
var onToolbarOver = function()
{
    toolbarHovered = true;
    clearTimeout(toolbarTimeoutId);
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

    clearTimeout(toolbarTimeoutId);
    if (toolbarOpened && mouseY > height) {
        toolbarTimeoutId = setTimeout("onHideTimeout()", 200);
    } else if (!toolbarOpened && mouseY <= height) {
        toolbarMoving = true;
        toolbar.fadeIn(200, onToolbarOpened);
    }
};
var onMouseLeave = function(event)
{
    clearTimeout(toolbarTimeoutId);
    toolbarTimeoutId = setTimeout("onHideTimeout()", 200);
};
var onHideTimeout = function()
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
        var scrollY = title.position().top - toolbar.height();
        $(document).scrollTop(scrollY);
    });
});



// Search engine
var onSearchKeyUp = function(event)
{
    if (event.keyCode == '13') {
        searchInput.addClass("loading");
        searchResult.css('display', 'none');

        var term = searchInput.val();
        $.getJSON(SCRIPTS_URL+'/search.php', {term: term}, onSearch);
    }
};
var onSearch = function(data)
{
    searchInput.removeClass("loading");
    searchResult.css('display', 'block');

    var count = data.length;
    var html = '<h1>'+count;
    if (count > 1) {
        html += ' results';
    } else {
        html += ' result';
    }
    html += '</h1>';

    html += '<ul>';
    $.each(data, function(index, link)
    {
        html += '<li><a href="'+link+'">'+link+'</a></li>';
    });
    html += '</ul>';

    searchResult.html(html);
};
var search = $(document.createElement('div'));
search.attr('id', 'search');
toolbar.append(search);
var searchInput = $(document.createElement('input'));
searchInput.keyup(onSearchKeyUp);
search.append(searchInput);
var searchResult = $(document.createElement('div'));
searchResult.attr('id', 'searchResult');
search.append(searchResult);
