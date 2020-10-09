<template>
<div>
    <div class="whole">
        <img src="@/assets/images/404back.jpg" />
        <div class="mask"></div>
    </div>
    <div class="b">
        <img src="@/assets/images/404.png" class="center"/>
        <p>
            暂时未能找到您查找的页面<br>
            可能输入的网址错误或此页面不存在<br>
        </p>
    </div>
</div>
</template>

<style scoped>
.page-container {font-size: 20px;text-align: center;color: rgb(192, 204, 218);}
.center{margin:0 auto;}
.whole{width:100%;height:100%;line-height:100%;position:fixed;bottom:0;left:0;z-index:-1000;overflow:hidden;}
.whole img{width:100%;height:100%;}
.mask{width:100%;height:100%;position:absolute;top:0;left:0;background:#000;opacity:0.6;filter:alpha(opacity=60);}
.b{width:100%;text-align:center;height:400px;position:absolute;top:50%;margin-top:-230px}.a{width:150px;height:50px;margin-top:30px}.a a{display:block;float:left;width:150px;height:50px;background:#fff;text-align:center;line-height:50px;font-size:18px;border-radius:25px;color:#333}.a a:hover{color:#000;box-shadow:#fff 0 0 20px}
p{color:#fff;margin-top:40px;font-size:24px; line-height:56px;}
#num{margin:0 5px;font-weight:bold;}
</style>
