<template>
  <view class="container">
    <swiper class="swiper" indicator-active-color="{{indicatorActiveColor}}" indicator-dots="{{indicatorDots}}" autoplay="{{autoplay}}" interval="{{interval}}" duration="{{duration}}" circular="true">
        <block v-for="item in adList" >
        <swiper-item>
          <image src="{{item.picture_url}}" class="slide-image" @tap="goToAdvert({{item.link_url}})" />
        </swiper-item>
      </block>
    </swiper>
    <view class="pos">
      <view class="search_read_only">
        <navigator class="search_content" open-type="navigate" url="/pages/search">
          <i class="iconfont icon-search"></i>
          <view class="search_input">搜索商品</view>
        </navigator>
        <navigator class="message" url="/pages/messages">
          <i class="iconfont icon-message cfff"></i>
          <view class="doc cfff">消息</view>
        </navigator>
      </view>
    </view>

      <!--矩阵商品列表模块-->
    <shopGridList :purchasetype.sync="purchasetype" :list.sync="list"></shopGridList>
    <!--加载更多时动画-->
    <bottomLoadMore :show.sync="showLoading" message="正在加载"></bottomLoadMore>
    <!--暂无数据显示-->
    <placeholder :show.sync="is_empty" message="暂无发现数据"></placeholder>
    </view>
</template>
<script>
import wepy from 'wepy';
import api from '@/api/api';
import tip from '@/utils/tip'
import Discover from '@/components/discover'
import ShopGridList from '@/components/shop_grid_list'
import BottomLoadMore from "@/components/common/bottomLoadMore"
import Placeholder from "@/components/common/placeholder"
import {
  USER_SPECICAL_INFO
} from '@/utils/constant';
import regeneratorRuntime from '@/utils/runtime.js'
export default class Home extends wepy.page {
  config = {
    navigationBarTitleText: '演示商城',
  }
  components = {
    discover: Discover,
    shopGridList: ShopGridList,
    bottomLoadMore: BottomLoadMore,
    placeholder: Placeholder
  }
  data = {
    indicatorDots: true,
    autoplay: true,
    interval: 3000,
    duration: 1000,
    indicatorActiveColor: "#fff",
    discoverList: [],
    //是否有数据
    is_empty: false,
    //当前页面
    currentPage: 1,
    //总页数
    page_total: 0,
    //是否显示 底部loading
    showLoading: true,
    //防止重复加载
    preventRepeatReuqest: false,
    //广告列表
    adList: [],
    tps: 0,
    is_show_alert: true,
    catCode: "",
    cate:{},
    list: [],
    purchasetype: 1,
    is_empty: false,
    //当前页面
    currentPage: 1,
    //总页数
    page_total: 0,
    //是否显示 底部loading
    showLoading: true,
    //防止重复加载
    preventRepeatReuqest: false,
    sort: 1,
    skuval: ""
  }

  async getGoodList(currentPage, size) {
    let that = this;
    let userSpecialInfo = wepy.getStorageSync(USER_SPECICAL_INFO) || {};
    let openid = userSpecialInfo.openid;
    const json = await api.product({
      query: {
        openid:openid,
        page: currentPage || 1,
        size: size || 10,
      }
    });
     if (json.statusCode === 200) {
      that.list = [...that.list, ...json.data.data];
      that.total_pages = json.data.meta.pagination.total_pages;
      if (json.data.meta.pagination.total_pages == 0) {
        //暂无数据
        that.is_empty = true;
      }
    } else {
      tip.error(json.data.msg);
    }
    that.showLoading = false;
    that.$apply();
  }


  async getAdList() {
    const json = await api.adList({
      query: {}
    });
   if (json.statusCode === 200) {
      this.adList = json.data.data;
      this.$apply();
    } else {

    }
  }
  onLoad() {
    let that = this;
    this.discoverList = [];
    this.getAdList();

    this.cate={};
    this.list = [];
    this.skuval = "";

    this.is_empty = false;
    //当前页面
    this.currentPage = 1;
    //总页数
    this.page_total = 0;
    //是否显示 底部loading
    this.showLoading = true;
    //防止重复加载
    this.preventRepeatReuqest = false;
    this.sort = 1;
    this.getGoodList();
  }
  computed = {}
  methods = {
    currentType(obj) {
      //tip.success("状态:" + obj);
      var name = obj.name;
      var type = obj.type;
      if (name=="zhonghe") {
        this.sort = -1;
      } else if (name=="sale") {
        this.sort = 3;
      } else if (name=="price") {
        if (type=="desc") {
          this.sort = 2;
        } else if (type=="asc") {
          this.sort = 1;
        }
      }else if (name == "sku") {
        this.skuval = type;
      }
      this.list = [];
      this.showLoading = true;
      this.is_empty = false;
      this.getGoodList();
    },
    goToAdvert(url) {
      if (url.length == 0) {

        return;
      }
      wepy.navigateTo({
        url: url
      })
    },
    onShareAppMessage: function(res) {
      if (res.from === 'button') {
        // 来自页面内转发按钮
      }
      return {
        title: '商城',
        path: '/pages/home',
        success: function(res) {
          // 转发成功
        },
        fail: function(res) {
          // 转发失败
        }
      }
    },
    alertCallback() {
      tip.alert('跳转');
    },
    closeAlert() {
     // tip.alert('关闭');
    }
  }
  events = {}
  //加载更多
  onReachBottom() {
    let that = this;
    that.showLoading = true;
    //判断总页数是否大于翻页数
    if ((that.total_pages) > that.currentPage) {
      //防止重复加载
      if (that.preventRepeatReuqest) {
        return true;
      }
      that.preventRepeatReuqest = true;
      that.currentPage++;
      that.getGoodList(that.currentPage);
      that.preventRepeatReuqest = false;
    } else {
      that.showLoading = false;
    }
  };
}

</script>
<style lang="less">
.swiper {
  height: 348rpx;
}

.slide-image {
  width: 100%;
  height: 100%;
}

.pos {
  position: absolute;
  top: 0rpx;
  left: 0;
  right: 0;
  .search_content {
    background: rgba(0, 0, 0, 0.1);
    border: 1px solid #efefee;
    .icon-search,
    .search_input {
      color: #efefee;
    }
  }
  .message {
    display: block;
    text-align: center;
    margin-left: 20rpx;
  }
  .doc {
    font-size: 16rpx;
    display: block;
  }
}


.nav_list {
  color: #404040;
  display: flex;
  font-size: 26rpx;
  justify-content: space-between;
  padding: 17rpx 50rpx;
  navigator {
    text-align: center
  }
  .nav_icon {
    height: 80rpx;
    margin: 0 auto;
    width: 80rpx;
    margin-bottom: 14rpx;
  }
  .nav_text {
    font-size: 26rpx
  }
}

.recommend-title {
  padding: 40rpx 0;
  text-align: center;
  color: #333;
}

</style>
