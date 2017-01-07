function modal_o(id) {
          // Inline Admin-Form example
          $.magnificPopup.open({
            removalDelay: 500, //delay removal by X to allow out-animation,
            items: {
              src: id
            },
            // overflowY: 'hidden', //
            callbacks: {
              beforeOpen: function (e) {
                var Animation = 'mfp-zoomIn';
                this.st.mainClass = Animation;
              }
            },
            midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
          });
        };
   var Stacks = {
          stack_top_right: {
            "dir1": "down",
            "dir2": "left",
            "push": "top",
            "spacing1": 10,
            "spacing2": 10
          },
          stack_top_left: {
            "dir1": "down",
            "dir2": "right",
            "push": "top",
            "spacing1": 10,
            "spacing2": 10
          },
          stack_bottom_left: {
            "dir1": "right",
            "dir2": "up",
            "push": "top",
            "spacing1": 10,
            "spacing2": 10
          },
          stack_bottom_right: {
            "dir1": "left",
            "dir2": "up",
            "push": "top",
            "spacing1": 10,
            "spacing2": 10
          },
          stack_bar_top: {
            "dir1": "down",
            "dir2": "right",
            "push": "top",
            "spacing1": 0,
            "spacing2": 0
          },
          stack_bar_bottom: {
            "dir1": "up",
            "dir2": "right",
            "spacing1": 0,
            "spacing2": 0
          },
          stack_context: {
            "dir1": "down",
            "dir2": "left",
            "context": $("#stack-context")
          },
        };

   // PNotify Plugin Event Init
       function notif(style, title_in, text_in) {
          var noteStyle = style;
          var noteShadow = true;
          var noteOpacity = '1';
          var noteStack = 'stack_bar_bottom';
          var width = "290px";
          // If notification stack or opacity is not defined set a default
          var noteStack = noteStack ? noteStack : "stack_top_right";
          var noteOpacity = noteOpacity ? noteOpacity : "1";
          // We modify the width option if the selected stack is a fullwidth style
          function findWidth() {
            if (noteStack == "stack_bar_top") {
              return "100%";
            }
            if (noteStack == "stack_bar_bottom") {
              return "70%";
            } else {
              return "290px";
            }
          }
   
      
	   new PNotify({
            title: title_in,
            text: text_in,
            shadow: noteShadow,
            opacity: noteOpacity,
            addclass: noteStack,
            type: noteStyle,
            stack: Stacks[noteStack],
            width: findWidth(),
            delay: 1400
          });
		 };
		 
	


