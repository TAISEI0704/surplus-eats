window.addEventListener("load", function(){

    //プラグインを定義
    gsap.registerPlugin(ScrollTrigger);
  
    const area  = document.querySelector(".js-area");
    const wrap  = document.querySelector(".js-wrap");
    const items = document.querySelectorAll(".js-item");
    const num   = items.length;
  
    //横幅を指定
    // gsap.set(wrap,  { width: num * 100 + "%" });
    gsap.set(wrap,  { width: "400%" });
    gsap.set(items, { width: 100 / num + "%" });
  
    gsap.to(items, {
      xPercent: -100 * ( num - 1 ), //x方向に移動させる
      ease: "none",
      scrollTrigger: {
        trigger: area, //トリガー
        start: "top top", //開始位置
        end: "+=10000", //終了位置
        pin: true, //ピン留め
        scrub: true, //スクロール量に応じて動かす
      }
    });
  });
  
  function myFunction() {
    alert("Button clicked!");
  }
  
  const lt = document.getElementById('lt');
  const gt = document.getElementById('gt');
  const carousel = document.querySelector('.carousel');
  const boxes = document.querySelectorAll('.box');
  let index = 0;
  
  function updatebtn() {
    lt.classList.remove('hidden');
    gt.classList.remove('hidden');
  
    if (index === 0) {
      lt.classList.add('hidden');
    }
  
    if (index === boxes.length - 1) {
      gt.classList.add('hidden');
    }
  }
  
  function moveBoxes() {
    const boxWidth = boxes[0].getBoundingClientRect().width;
    carousel.style.transform = `translateX(${-1 * boxWidth * index}px)`;
  }
  
  updatebtn();
  
  gt.addEventListener('click', () => {
    index++;
    updatebtn();
    moveBoxes();
  });
  
  lt.addEventListener('click', () => {
    index--;
    updatebtn();
    moveBoxes();
  });
  
  window.addEventListener('resize', () => {
    moveBoxes();
  });