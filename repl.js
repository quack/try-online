var QuackRepl = (function () {
  function QuackRepl() {
    this.stdout = document.querySelector('#quack-repl-stdout');
    this.stdin = document.querySelector('#quack-repl-stdin');
    this.input = document.querySelector('#quack-repl-stdin-input');
  }

  QuackRepl.prototype.configureStdin = function () {
    var that = this;
    this.stdin.onclick = function () {
      that.input.focus();
      that.input.selectionStart = that.input.selectionEnd = that.input.value.length;
    };
    this.input.onkeypress = function (e) {
      if (e.which === 13 || e.keyCode === 13) {
        that.send(that.input.value);
      }
    };
    this.input.onkeyup = function (e) {
      var key = e.which || e.keyCode;
      if (key === 38) {
        var last = that.history[that.history.length - 1 - that.level];
        if (last) {
          that.input.value = last;
        }

        that.level = Math.min(that.level + 1, that.history.length);
      }
    }
  };

  QuackRepl.prototype.ajax = function (text) {
    var xhr = new XMLHttpRequest();
    var that = this;
    xhr.open('GET', './api.php?action=' + encodeURIComponent(btoa(text)));
    xhr.send(null);
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4) {
        xhr.responseText.split('\n').forEach(function (line) {
          that.input.value = "";
          that.print(line);
        });
        that.scrollBottom();
      }
    };
  };

  QuackRepl.prototype.send = function (text) {
    text = text.trim();
    this.history = this.history || [];
    this.level = 0;

    switch (text) {
      case ":clear":
        this.stdout.innerHTML = "";
        this.history.push(':clear');
        break;
      case 'show c':
        text = ':license'
      default:
        this.print('<span class="quack-repl-name">Quack&gt;</span> ' + text);
        if (text !== '') {
          this.ajax(text);
        }
        this.history.push(text);
        break;
    }

    this.input.value = "";
    this.scrollBottom();
  };

  QuackRepl.prototype.print = function (text) {
    this.stdout.appendChild((function () {
      var div = document.createElement('div');
      div.className = 'quack-repl-line';
      div.innerHTML = text;
      return div;
    })());
  }

  QuackRepl.prototype.scrollBottom = function () {
    window.scrollTo(0, document.body.scrollHeight);
  };

  QuackRepl.prototype.welcome = function () {
    var that = this;
    [ "Quack · Copyright (C) 2016 Marcelo Camargo &lt;marcelocamargo@linuxmail.org&gt;",
      "This program comes with ABSOLUTELY NO WARRANTY.",
      "This is free software, and you are welcome to redistribute it",
      "under certain conditions; type 'show c' for details.",
      "Use quack --help for more information",
    ].forEach(function (line) {
      that.print(line);
    });

    that.input.focus();
    that.input.selectionStart = that.input.selectionEnd = that.input.value.length;
  }

  return QuackRepl;
})();

(function (module) {
  module.repl = new QuackRepl();
  module.repl.configureStdin();
  module.repl.welcome();
})(window);
