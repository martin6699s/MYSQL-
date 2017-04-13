1. 先安装es6转码器babel
首先在项目空目录上创建一个package.json文件
在里面加入
```
{
  "name": "my-es6",
  "version": "1.0.0",
  "devDependencies": {
  }
}
```
其中my-es6是项目名称，接下来在项目中安装babel:

```
martin@www:~/Documents/es6$ npm install --save-dev babel-cli
```
安装完成后，在package.json里就有多了些依赖
```
{
  "name": "my-es6",
  "version": "1.0.0",
  "devDependencies": {
    "babel-cli": "^6.24.0"
  }
}
```