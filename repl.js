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
  };

  QuackRepl.prototype.send = function (text) {
    text = text.trim();

    switch (text) {
      case ":clear":
        this.stdout.innerHTML = "";
        break;
      default:
        this.print('<span class="quack-repl-name">Quack&gt;</span> ' + text);
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
    [ "Quack · Copyright (C) 2016 Marcelo Camargo",
      "This program comes with ABSOLUTELY NO WARRANTY.",
      "This is free software, and you are welcome to redistribute it",
      "under certain conditions; type 'show c' for details.",
      "Use quack --help for more information",
    ].forEach(function (line) {
      that.print(line);
    });
  }

  return QuackRepl;
})();

(function (module) {
  module.repl = new QuackRepl();
  module.repl.configureStdin();
  module.repl.welcome();
})(window);


