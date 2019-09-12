# 简介
大多数网站的管理后台开发过程中都会有很多重复性的工作，因为所谓管理，无外乎就是增删改查，手工去写效率低而且容易出错。对于开发者来说，这些无意义的重复工作浪费时间，但是对个人提升没有任何意义。
于是这个框架诞生了，它的目的就是彻底抛弃重复的无意义工作，帮助开发者把精力放到更有价值的东西上去。
# 基本原理
* 正常情况下，用户看到一个网页的过程是这样的，这里有一个问题，就是**Controller和View的内容都需要我们自己编写代码**，大多数情况还会用到Model中的一些函数(这些内容我们在后面的讨论里忽略不计，因为一般情况下这些函数都是针对具体业务的函数，在这个通用框架里我们没法优化这部分内容)。
    ![43ec8510-8ae3-421a-b3be-d26f043ce8c5.png](https://raw.githubusercontent.com/qiao1994/thinkphp-admin/master/readme_files/43ec8510-8ae3-421a-b3be-d26f043ce8c5.png)

* 框架要解决Controller和View需要人工编写的问题，Controller 的核心需求是对数据进行增删改查操作，View 的核心需求是展现数据，它们都需要根据当前数据的类型进行处理。如果能**让程序知道当前每一个数据的类型**，并且针对不同类型的数据进行对应的处理(Controller的增删改查；View的展现)，那开发者就彻底摆脱了这些无聊的重复劳动。为了让程序知道每个数据对应的类型，我们把每个数据的属性配置在了Model文件里，即：**一个表对应一个Model文件，每个表的Model文件中配置了这个表的每一列的数据类型、中文名称、是否需要特殊处理等**，这样我们就可以让程序自动去处理每一列信息。
    * **Controller**   通过继承AdminController，根据Model文件中的配置，获得了基本的增删改查操作能力，如果有特殊需求，你可以在Model文件中指定，或者在对应的Controller中调用AdminController的前后置操作接口(举例：增加前、修改前...)
    * **View** 每次Controller被调用，它的父类AdminController就会执行一些初始化操作，其中包括，根据Model中的配置生成View(html文件)，最后再用Controller进行展现。
    * 这样，一次请求的过程就变成了如下图所示，我们要做的事情也只剩下了编辑Model文件，在其中指定每个列的数据类型及特殊需求内容。
    ![2316ffeb-d8aa-4309-80ca-edcd23fc6dd9.png](https://raw.githubusercontent.com/qiao1994/thinkphp-admin/master/readme_files/2316ffeb-d8aa-4309-80ca-edcd23fc6dd9.png)
# 使用方法
* 部署
    * 按照正常的php程序部署就可以，注意需要进行URL美化，去掉thinkphp的`index.php`，我们的URL_MODEL是2。需要对服务器应用进行配置，参考：http://document.thinkphp.cn/manual_3_2.html#url_rewrite
    * 导入数据库，并修改配置文件中对应的mysql用户名&密码，文件位置`/App/Common/Conf/config.php`
* 基本使用   我们通过创建几个示例数据表进行管理来演示框架的使用方法
    * ## 创建数据表
        * 根据需求创建数据表，这里做一个图书管理，创建三个数据表，书籍表`book`、书籍分类表`cate`、书籍标签表`tag`，三个表的结构分别如下。
            * **书籍表`book`**
                ![eed95974-704f-4bac-bbe6-89bb863024fd.png](https://raw.githubusercontent.com/qiao1994/thinkphp-admin/master/readme_files/eed95974-704f-4bac-bbe6-89bb863024fd.png)
            * **书籍分类表`cate`**
                ![459e0293-ac6e-4af5-88d9-eba6e959db37.png](https://raw.githubusercontent.com/qiao1994/thinkphp-admin/master/readme_files/459e0293-ac6e-4af5-88d9-eba6e959db37.png)
            * **书籍标签表`tag`**
                ![30793257-e2ae-4f8f-83f7-e0854f916a70.png](https://raw.githubusercontent.com/qiao1994/thinkphp-admin/master/readme_files/30793257-e2ae-4f8f-83f7-e0854f916a70.png)
        * 建表时有一些小细节需要注意
            * 尽量使用`id`作为每一个表的主键，并且设置自增`AUTO_INCREMENT`
            * `create_time`和`update_time`在建表的时候不需要添加，后续框架会自动为你添加这两个列
            * 当需要关联外键时，使用`表名_id`的形式，比如`book_id`和`cate_id`
            * 给表的列添加注释，这样是一个良好的习惯，这也会在后面的操作中让你更加省力，我还在注释中给出了这个列在编辑的时候需要什么样的控件
    * ## 生成Model文件
        * 接下来我们要做我们唯一需要做的事情，生成Model文件，这个文件里会记录表中每个列的数据类型等很多信息，这些信息足够支持程序知道如何展示和修改每一条数据
        * 访问`你的URL/Admin`，使用默认用户名`admin`，默认密码`123456`登录，这里就是我们的后台页面，同时本次登录让你有权限访问后续的页面
        * 访问`你的URL/Auto`，这个页面有三块功能：生成文件、生成CRUD代码、删除文件(在下面，慎用)，当前我们需要用到的是生成文件功能，在`生成文件`一栏选择对应的数据表
        * 这里以**`book`**表为例，介绍生成页面各个选项的含义，这些选项最终都会生成到Model文件里，你也可以后续到Model文件里进行修改。
![f842dd8b-18cc-4374-9278-6ce7ca21d67a.png](https://raw.githubusercontent.com/qiao1994/thinkphp-admin/master/readme_files/f842dd8b-18cc-4374-9278-6ce7ca21d67a.png)
![6d3be886-9ed6-4c28-a653-33abea026be2.png](https://raw.githubusercontent.com/qiao1994/thinkphp-admin/master/readme_files/6d3be886-9ed6-4c28-a653-33abea026be2.png)
        * 生成完成，我们就可以在`你的URL/Admin`页面看到，多了一个书籍管理，至此，一个最基本的书籍增删改查操作完成。
    * ## Model文件解读
        * 我们来看一下刚刚的操作生成了怎样的一个Model文件以及其中各项的意义，Model文件中的`$fieldMap`属性，定义了每一列的特性，真正操作数据的过程中，Controller和View都需要从Model中读取配置
        * 生成的Model文件默认路径是 `/App/Common/Model`
![48069b42-b9ca-4287-9bd3-70f1c62d799e.png](https://raw.githubusercontent.com/qiao1994/thinkphp-admin/master/readme_files/48069b42-b9ca-4287-9bd3-70f1c62d799e.png)
    * ## Controller文件解读
        * 生成操作除了生成Model文件，还会生成一个对应的Controller文件，本例中就生成了`/App/Admin/Controller/BookController.class.php`文件，默认的Controller文件只是继承了父类AdminController，给出了一些前后置操作接口，供需要时调用，具体逻辑可以查看`/ThinkPHP/Library/Org/Util/AdminController.class.php.deploy`中的内容
        * 建议仔细阅读`create()` `delete()` `update()` `list()` `import()` `export()`几个函数的源码，尤其要关注前后置操作接口调用的位置，这样当后续开发中需要用到这些特殊操作时才能得心应手
        * 还需要了解`importValueHandle()导入前处理`和`handleSearchMap()搜索时处理`两个函数对于特殊类型(主要是外键)字段的特殊处理
# 其他内容
* 开发过程中配置文件`/App/Common/Conf/config.conf`中`NOT_AUTO_GENERATE`是关闭的，意味着每次访问都会重新生成View文件，如果有些页面实在不能通过修改Model和Controller搞定，一定要修改html文件的内容才可以，那么需要在`NOT_AUTO_GENERATE`处指定不需要生成的页面，同时备注一定要这么做的原因。当项目上线，则需要关闭`NOT_AUTO_GENERATE`，这时View文件不会被重新生成