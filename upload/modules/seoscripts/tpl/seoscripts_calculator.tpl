  <script language='JavaScript'>

    Memory  = "0";
    Current = "0";
    Operation = 0;
    MAXLENGTH = 30;

function AddDigit(dig) 
 { if (Current.indexOf("!") == -1) 
    { if (    (eval(Current) == 0)
              && (Current.indexOf(".") == -1)
         ) { Current = dig;
           } else
           { Current = Current + dig;
           };
      Current = Current.toLowerCase();
    } else
    { Current = "Hint! Press 'AC'"; 
    };
   if (Current.indexOf("e0") != -1)
     { var epos = Current.indexOf("e");
       Current = Current.substring(0,epos+1) + Current.substring(epos+2);
     };
  if (Current.length > MAXLENGTH)
     { Current = "Too long"; 
     };
   document.Calculator.Display.value = Current;
 }

function Dot() 
 {
  if ( Current.length == 0)  
    { Current = "0.";
    } else
    {  if (   ( Current.indexOf(".") == -1)
            &&( Current.indexOf("e") == -1)
          )
         { Current = Current + ".";
    };   };
  document.Calculator.Display.value = Current;
 }

function DoExponent()
 {
  if ( Current.indexOf("e") == -1 )
       { Current = Current + "e0";
         document.Calculator.Display.value = Current;
       };
 }

function PlusMinus()
 {
  if  (Current.indexOf("e") != -1)
    { var epos = Current.indexOf("e-");
      if (epos != -1)
         { Current = Current.substring(0,1+epos) + Current.substring(2+epos);
         } else
         { epos = Current.indexOf("e");
           Current = Current.substring(0,1+epos) + "-" + Current.substring(1+epos); 
         };
    } else
    {  if ( Current.indexOf("-") == 0 )
         { Current = Current.substring(1);
         } else
         { Current = "-" + Current;
         };
       if (    (eval(Current) == 0)
            && (Current.indexOf(".") == -1 )
          ) { Current = "0"; };
    };
  document.Calculator.Display.value = Current;
 }

function Clear()  
 { Current = "0";
   document.Calculator.Display.value = Current;
 }

function AllClear()
 { Current = "0";
   Operation = 0;
   Memory = "0"; 
   document.Calculator.Display.value = Current;
 }

function Operate(op)  
 {
 if (Operation != 0) { Calculate(); };

  if (op.indexOf("*") > -1) { Operation = 1; };   
  if (op.indexOf("/") > -1) { Operation = 2; };    
  if (op.indexOf("+") > -1) { Operation = 3; };    
  if (op.indexOf("-") > -1) { Operation = 4; };    

  Memory = Current;   
  Current = "";
  document.Calculator.Display.value = Current;
 }

function Calculate()    
 { 
  if (Operation == 1) { Current = eval(Memory) * eval(Current); };
  if (Operation == 2)
    { if (eval(Current) != 0)
      { Current = eval(Memory) / eval(Current)
      } else
      { Current = "Divide by zero"; 
      }
    };
  if (Operation == 3) { Current = eval(Memory) + eval(Current); };
  if (Operation == 4) { Current = eval(Memory) - eval(Current); };
  Operation = 0; 
  Memory = "0";    
  Current = Current + ""; 
  if (Current.indexOf("Infinity") != -1) 
    { Current = "Value too big";
    };
  if (Current.indexOf("NaN") != -1) 
    { Current = "I don't understand";
    };
  document.Calculator.Display.value = Current;
 }

function FixCurrent()
 {
  Current = document.Calculator.Display.value;
  Current = "" + parseFloat(Current);
  if (Current.indexOf("NaN") != -1)
    { Current = "I don't understand";
    };
  document.Calculator.Display.value = Current;
 }

  //--------------------------------------------------------------->
  </script>

