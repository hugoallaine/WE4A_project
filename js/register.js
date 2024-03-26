(function() {
    let currentPage = 1;
    const prevBtn = document.querySelector(".footer .prev");
    const nextBtn = document.querySelector(".footer .next");
    function movePage() {
        prevBtn.disabled = false;
        nextBtn.disabled = false;
        if(currentPage === 1) {
            prevBtn.disabled = true;
        } else if(currentPage === 3) {
            nextBtn.disabled = true;
        }
        const stepNode = document.querySelector(".steps .step");
        const width = ((currentPage-1)*stepNode.offsetWidth*-1)+"px";
        stepNode.parentNode.style.marginLeft = width;
    }
    prevBtn.addEventListener("click", function() {
        currentPage -= 1;
        movePage();
    });
    nextBtn.addEventListener("click", function() {
        currentPage += 1;
        movePage();
    });
})();