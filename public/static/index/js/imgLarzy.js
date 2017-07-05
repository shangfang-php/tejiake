/**
 * Created by yangbys168 on 2016-01-14 15:05:51
 * url: http://www.qdfuns.com/notes/15548/9376db07e06b5ac998d614223d4ff4e6
 */
var lazyload = {
    init : function(opt){
        var that = this,
            op = {
                anim: true,
                extend_height:0,
                selectorName:"img",
                realSrcAtr:"data-img"
            };
        // �ϲ��������е�{anim:true}+�û��Զ������Ҳ����op = op + opt
        $.extend(op,opt);
        // ����lazyload.img.init(op)����
        that.img.init(op);

    },

    img : {
        init : function(n){

            var that = this,
                selectorName = n.selectorName,
                realSrcAtr = n.realSrcAtr,
                anim = n.anim;

            // Ҫ���ص�ͼƬ�ǲ�����ָ��������
            function inViewport( el ) {
                // ��ǰ���ڵĶ���
                var top = window.pageYOffset,
                    // ��ǰ���ڵĵײ�
                    btm = window.pageYOffset + window.innerHeight,
                    // Ԫ����������ҳ���ڵ�y��λ��
                    elTop = $(el).offset().top;
                // �ж�Ԫ�أ��Ƿ��ڵ�ǰ���ڣ����ߵ�ǰ��������400������
                return elTop >= top && elTop - n.extend_height <= btm;
            }

            // �����¼����жϣ�����ͼƬ
            $(window).on('scroll', function() {
                $(selectorName).each(function(index,node) {
                    var $this = $(this);

                    if(!$this.attr(realSrcAtr) || !inViewport(this)){
                        return;
                    }

                    act($this);

                })
            }).trigger('scroll');

            // չʾͼƬ
            function act(_self){
                // �Ѿ����ع��ˣ����жϺ�������
                if (_self.attr('lazyImgLoaded')) return;
                var img = new Image(),
                    original = _self.attr('data-img');
                // ͼƬ������ɺ���¼�����data-imgָ����ͼƬ���ŵ�src���棬�������ʾ
                img.onload = function() {
                    _self.attr('src', original);
                    anim && _self.css({ opacity: .2 }).animate({ opacity: 1 }, 280);
                };
                // ��������img.src��ʱ����������ڷ���ͼƬ������
                original && (img.src = original);
                _self.attr('lazyImgLoaded', true);
            }
        }
    }
};