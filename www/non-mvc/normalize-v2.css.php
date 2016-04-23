<?php

require dirname(__FILE__).'/../config.php';
require $PLATFORMCREATE;

ci()->load
    ->helper('url')
    ->library('template')
;

echo html_begin();

echo head_begin();
echo meta_charset();

?>

        <meta name="viewport" content="width=device-width">
        <title>Normalize CSS</title>
<?php

echo favicon();
echo apple_touch_icon_precomposed();
echo cleartype_ie();

echo css('lib/normalize-2/normalize.css');
echo js('lib/html5shiv/html5shiv.min.js');

?>

        <style>
            #boxsize button,
            #boxsize input,
            #boxsize select,
            #boxsize textarea {
                width: 200px;
                padding: 5px;
                border: 1px solid #333;
            }
        </style>

<?php

echo js_platform();
echo js_selectivizr();
//echo js_modernizr();
echo js_respond();
echo js_jquery();

echo head_end();
echo body_tag('id="page-top"');

?>

        <p style="margin-top: 25px;">
            <a href="<?php echo site_url('playground'); ?>">Back to the playground</a>
        </p>

        <h1>Heading 1</h1>
        <h2>Heading 2</h2>
        <h3>Heading 3</h3>
        <h4>Heading 4</h4>
        <h5>Heading 5</h5>
        <h6>Heading 6</h6>

        <section>
            <h1>Heading 1 (in section)</h1>
            <h2>Heading 2 (in section)</h2>
            <h3>Heading 3 (in section)</h3>
            <h4>Heading 4 (in section)</h4>
            <h5>Heading 5 (in section)</h5>
            <h6>Heading 6 (in section)</h6>
        </section>

        <article>
            <h1>Heading 1 (in article)</h1>
            <h2>Heading 2 (in article)</h2>
            <h3>Heading 3 (in article)</h3>
            <h4>Heading 4 (in article)</h4>
            <h5>Heading 5 (in article)</h5>
            <h6>Heading 6 (in article)</h6>
        </article>

        <header>
            <hgroup>
                <h1>Heading 1 (in hgroup)</h1>
                <h2>Heading 2 (in hgroup)</h2>
            </hgroup>
            <nav>
                <ul>
                    <li><a href="#">navigation item #1</a></li>
                    <li><a href="#">navigation item #2</a></li>
                    <li><a href="#">navigation item #3</a></li>
                </ul>
            </nav>
        </header>

        <h1>Text-level semantics</h1>

        <p hidden>This should be hidden in all browsers, apart from IE6</p>

        <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et m. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et m. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et m.</p>
        <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et m. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et m. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et m.</p>

        <address>Address: somewhere, world</address>

        <hr>

        <hr style="height:4px; border:solid #000; border-width:1px 0;">

        <p>
        The <a href="#">a element</a> example<br>
        The <abbr>abbr element</abbr> and <abbr title="Title text">abbr element with title</abbr> examples<br>
        The <b>b element</b> example<br>
        The <cite>cite element</cite> example<br>
        The <code>code element</code> example<br>
        The <del>del element</del> example<br>
        The <dfn>dfn element</dfn> and <dfn title="Title text">dfn element with title</dfn> examples<br>
        The <em>em element</em> example<br>
        The <i>i element</i> example<br>
        The img element <img src="http://lorempixel.com/16/16" alt=""> example<br>
        The <ins>ins element</ins> example<br>
        The <kbd>kbd element</kbd> example<br>
        The <mark>mark element</mark> example<br>
        The <q>q element <q>inside</q> a q element</q> example<br>
        The <s>s element</s> example<br>
        The <samp>samp element</samp> example<br>
        The <small>small element</small> example<br>
        The <span>span element</span> example<br>
        The <strong>strong element</strong> example<br>
        The <sub>sub element</sub> example<br>
        The <sup>sup element</sup> example<br>
        The <u>u element</u> example<br>
        The <var>var element</var> example
        </p>

        <h1>Template content</h1>
        <template>
            <h1>{{title}}</h1>
            <content></content>
        </template>

        <h1>Embedded content</h1>

        <h3>audio</h3>

        <audio controls></audio>
        <audio></audio>

        <h3>img</h3>

        <img src="http://lorempixel.com/100/100" alt="">
        <a href="#"><img src="http://lorempixel.com/100/100" alt=""></a>

        <h3>svg</h3>

        <svg width="100px" height="100px">
            <circle cx="100" cy="100" r="100" fill="#ff0000" />
        </svg>

        <h3>video</h3>

        <video controls></video>
        <video></video>

        <h1>Interactive content</h1>

        <h3>details / summary</h3>
        <details>
            <summary>More info</summary>
            <p>Additional information</p>
            <ul>
                <li>Point 1</li>
                <li>Point 2</li>
            </ul>
        </details>

        <h1>Grouping content</h1>

        <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et m.</p>

        <h3>pre</h3>

        <pre>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et me.</pre>

        <pre><code>&lt;html>
    &lt;head>
    &lt;/head>
    &lt;body>
        &lt;div class="main"> &lt;div>
    &lt;/body>
&lt;/html></code></pre>

        <h3>blockquote</h3>

        <blockquote>
            <p>Some sort of famous witty quote marked up with a &lt;blockquote> and a child &lt;p> element.</p>
        </blockquote>

        <blockquote>Even better philosophical quote marked up with just a &lt;blockquote> element.</blockquote>

        <h3>ordered list</h3>

        <ol>
            <li>list item 1</li>
            <li>list item 1
                <ol>
                    <li>list item 2</li>
                    <li>list item 2
                        <ol>
                            <li>list item 3</li>
                            <li>list item 3</li>
                        </ol>
                    </li>
                    <li>list item 2</li>
                    <li>list item 2</li>
                </ol>
            </li>
            <li>list item 1</li>
            <li>list item 1</li>
        </ol>

        <h3>unordered list</h3>

        <ul>
            <li>list item 1</li>
            <li>list item 1
                <ul>
                    <li>list item 2</li>
                    <li>list item 2
                        <ul>
                            <li>list item 3</li>
                            <li>list item 3</li>
                        </ul>
                    </li>
                    <li>list item 2</li>
                    <li>list item 2</li>
                </ul>
            </li>
            <li>list item 1</li>
            <li>list item 1</li>
        </ul>

        <h3>description list</h3>

        <dl>
            <dt>Description name</dt>
            <dd>Description value</dd>
            <dt>Description name</dt>
            <dd>Description value</dd>
            <dd>Description value</dd>
            <dt>Description name</dt>
            <dt>Description name</dt>
            <dd>Description value</dd>
        </dl>

        <h3>figure</h3>

        <figure>
            <img src="http://lorempixel.com/400/200" alt="">
            <figcaption>Figcaption content</figcaption>
        </figure>

        <h1>Tabular data</h1>

        <table>
            <caption>Jimi Hendrix - albums</caption>
            <thead>
                <tr>
                    <th>Album</th>
                    <th>Year</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Album</th>
                    <th>Year</th>
                    <th>Price</th>
                </tr>
            </tfoot>
            <tbody>
                <tr>
                    <td>Are You Experienced</td>
                    <td>1967</td>
                    <td>$10.00</td>
                </tr>
                <tr>
                    <td>Axis: Bold as Love</td>
                    <td>1967</td>
                    <td>$12.00</td>
                </tr>
                <tr>
                    <td>Electric Ladyland</td>
                    <td>1968</td>
                    <td>$10.00</td>
                </tr>
                <tr>
                    <td>Band of Gypsys</td>
                    <td>1970</td>
                    <td>$12.00</td>
                </tr>
            </tbody>
        </table>

        <h1>Forms</h1>

        <form>
            <fieldset>
                <legend>Inputs as descendents of labels (form legend). This doubles up as a long legend that can test word wrapping.</legend>
                <p><label>Text input <input type="text" value="default value that goes on and on without stopping or punctuation"></label></p>
                <p><label>Email input <input type="email"></label></p>
                <p><label>Search input <input type="search"></label></p>
                <p><label>Tel input <input type="tel"></label></p>
                <p><label>URL input <input type="url" placeholder="http://"></label></p>
                <p><label>Password input <input type="password" value="password"></label></p>
                <p><label>File input <input type="file"></label></p>

                <p><label>Radio input <input type="radio" name="rad"></label></p>
                <p><label>Checkbox input <input type="checkbox"></label></p>
                <p><label><input type="radio" name="rad"> Radio input</label></p>
                <p><label><input type="checkbox"> Checkbox input</label></p>

                <p><label>Select field <select><option>Option 01</option><option>Option 02</option></select></label></p>
                <p><label>Textarea <textarea cols="30" rows="5" >Textarea text</textarea></label></p>
            </fieldset>

            <fieldset>
                <legend>Inputs as siblings of labels</legend>
                <p><label for="ic">Color input</label> <input type="color" id="ic" value="#000000"></p>
                <p><label for="in">Number input</label> <input type="number" id="in" min="0" max="10" value="5"></p>
                <p><label for="ir">Range input</label> <input type="range" id="ir" value="10"></p>
                <p><label for="idd">Date input</label> <input type="date" id="idd" value="1970-01-01"></p>
                <p><label for="idm">Month input</label> <input type="month" id="idm" value="1970-01"></p>
                <p><label for="idw">Week input</label> <input type="week" id="idw" value="1970-W01"></p>
                <p><label for="idt">Datetime input</label> <input type="datetime" id="idt" value="1970-01-01T00:00:00Z"></p>
                <p><label for="idtl">Datetime-local input</label> <input type="datetime-local" id="idtl" value="1970-01-01T00:00"></p>

                <p><label for="irb">Radio input</label> <input type="radio" id="irb" name="rad"></p>
                <p><label for="icb">Checkbox input</label> <input type="checkbox" id="icb"></p>
                <p><input type="radio" id="irb2" name="rad"> <label for="irb2">Radio input</label></p>
                <p><input type="checkbox" id="icb2"> <label for="icb2">Checkbox input</label></p>

                <p><label for="s">Select field</label> <select id="s"><option>Option 01</option><option>Option 02</option></select></p>
                <p><label for="t">Textarea</label> <textarea id="t" cols="30" rows="5" >Textarea text</textarea></p>
            </fieldset>

            <fieldset>
                <legend>Clickable inputs and buttons</legend>
                <p><input type="image" src="http://lorempixel.com/90/24" alt="Image (input)"></p>
                <p><input type="reset" value="Reset (input)"></p>
                <p><input type="button" value="Button (input)"></p>
                <p><input type="submit" value="Submit (input)"></p>
                <p><input type="submit" value="Disabled (input)" disabled></p>


                <p><button type="reset">Reset (button)</button></p>
                <p><button type="button">Button (button)</button></p>
                <p><button type="submit">Submit (button)</button></p>
                <p><button type="submit" disabled>Disabled (button)</button></p>
            </fieldset>

            <fieldset id="boxsize">
                <legend>box-sizing tests</legend>
                <div><input type="text" value="text"></div>
                <div><input type="email" value="email"></div>
                <div><input type="search" value="search"></div>
                <div><input type="url" value="http://example.com"></div>
                <div><input type="password" value="password"></div>

                <div><input type="color" value="#000000"></div>
                <div><input type="number" value="5"></div>
                <div><input type="range" value="10"></div>
                <div><input type="date" value="1970-01-01"></div>
                <div><input type="month" value="1970-01"></div>
                <div><input type="week" value="1970-W01"></div>
                <div><input type="datetime" value="1970-01-01T00:00:00Z"></div>
                <div><input type="datetime-local" value="1970-01-01T00:00"></div>

                <div><input type="radio"></div>
                <div><input type="checkbox"></div>

                <div><select><option>Option 01</option><option>Option 02</option></select></div>
                <div><textarea cols="30" rows="5">Textarea text</textarea></div>

                <div><input type="image" src="http://lorempixel.com/90/24" alt="Image (input)"></div>
                <div><input type="reset" value="Reset (input)"></div>
                <div><input type="button" value="Button (input)"></div>
                <div><input type="submit" value="Submit (input)"></div>

                <div><button type="reset">Reset (button)</button></div>
                <div><button type="button">Button (button)</button></div>
                <div><button type="submit">Submit (button)</button></div>
            </fieldset>
        </form>

<?php

echo js_jquery_extra_selectors();
echo js_bp_plugins();
echo js_mbp_helper();
echo js_scale_fix_ios();
echo js_imgsizer();

echo div_debug();

require $PLATFORMDESTROY;

?>
    </body>
</html>
