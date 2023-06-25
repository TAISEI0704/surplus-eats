window.addEventListener("load", function(){

    //プラグインを定義
    gsap.registerPlugin(ScrollTrigger);
  
    const area  = document.querySelector(".js-area");
    const wrap  = document.querySelector(".js-wrap");
    const items = document.querySelectorAll(".js-item");
    const num   = items.length;
  
    //横幅を指定
    // gsap.set(wrap,  { width: num * 100 + "%" });
    gsap.set(wrap,  { width: "320%" });
    gsap.set(items, { width: 70 / num + "%" });
  
    gsap.to(items, {
      xPercent: -100 * ( num - 1 ), //x方向に移動させる
      ease: "none",
      scrollTrigger: {
        trigger: area, //トリガー
        start: "center center", //開始位置
        end: "+=10000", //終了位置
        pin: true, //ピン留め
        scrub: true, //スクロール量に応じて動かす
      }
    });
  });
  