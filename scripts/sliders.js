const root = document.documentElement;
const df= [[125,55,70],[75,60,70],[190,70,70],[16,50,70]]
set=0
var clr=[]

// setting default colors
for (var i = 0; i < 4; i++){
    clr[i]=[...df[i]]
    type_set(i)
}

// selecting circle type: 3. all  0. first placer 1. final canvas 2. endgame
function type_set(x){
    set=x
    for (var i = 0; i < 3; i++){
        document.getElementById("c"+i).value=clr[set][i]
        setClr(i)
    }
}

// updating css variables
function setClr(x){
    val=document.getElementById("c"+x).value
    clr[set][x]=val
    if (x!=0) val+="%"
    root.style.setProperty('--s'+x, val);
    val=clr[set][0]+",100%,"+clr[set][1]+"%,"+clr[set][2]+"%"
    root.style.setProperty('--c'+set, val);
}

function default_clr(){
    clr[set]=[...df[set]]
    type_set(set)
}