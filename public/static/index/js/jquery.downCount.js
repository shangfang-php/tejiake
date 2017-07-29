/*
 * jQuery Form Plugin:jquery.downCount.js
 * ����ʱ���
 *
 * author:  damys
 * version: 1.1
 * url:     https://github.com/damys/lab-js/tree/master/downCount
 * Date:    2016/12
 */


(function ($) {
    $.fn.downCount = function (options, callback) {
        var configs = $.extend({
            date: null,
            offset:8,     // ʱ��+8
            milliShow:0   // �Ƿ���ʾ���� �� ע���������ڴ棬 ������ʹ��
        }, options);

        if (!configs.date) {
            $.error('Date is not defined.');
        }

        // ������ڸ�ʽ���ô����׳�����
        if (!Date.parse(configs.date)) {
            $.error('Incorrect date format, it should look like this, 12/24/2012 12:00:00.');
        }

        var currentDate = function () {
            var date = new Date();

            // turn date to utc
            var utc = date.getTime() + (date.getTimezoneOffset() * 60000);
            var new_date = new Date(utc + (3600000*configs.offset))

            return new_date;

            // ���£�location time
            // return new Date(date.getTime());
        };

        // ������������
        var container = this;

        // ����ʱ
        function countdown () {
            var target_date = new Date(configs.date), // ����Ŀ������
                current_date = currentDate();         // ��ȡ��̬��ǰ����

            // ���㲻ͬ����
            var difference = target_date - current_date;

            // �����ڲ�С��0ʱ�����
            if (difference < 0) {
                // stop timer
                clearInterval(interval);

                if (callback && typeof callback === 'function') {
                    callback();
                }
                return;
            }


            var _second = 1000,
                _minute = _second * 60,
                _hour = _minute * 60,
                _day = _hour * 24;

            // ��������
            var days = Math.floor(difference / _day),
                hours = Math.floor((difference % _day) / _hour),
                minutes = Math.floor((difference % _hour) / _minute),
                seconds = Math.floor((difference % _minute) / _second);

            // fix dates so that it will show two digets
            days = (String(days).length >= 2) ? days : '0' + days;
            hours = (String(hours).length >= 2) ? hours : '0' + hours;
            minutes = (String(minutes).length >= 2) ? minutes : '0' + minutes;
            seconds = (String(seconds).length >= 2) ? seconds : '0' + seconds;

            // ��ǰʱ������
            var ref_days = (days === 1) ? 'day' : 'days',
                ref_hours = (hours === 1) ? 'hour' : 'hours',
                ref_minutes = (minutes === 1) ? 'minute' : 'minutes',
                ref_seconds = (seconds === 1) ? 'second' : 'seconds';

            // ����DOM ʱ������
            container.find('.days').text(days);
            container.find('.hours').text(hours);
            container.find('.minutes').text(minutes);
            container.find('.seconds').text(seconds);

            // ����DOM ʱ����������
            container.find('.days_ref').text(ref_days);
            container.find('.hours_ref').text(ref_hours);
            container.find('.minutes_ref').text(ref_minutes);
            container.find('.seconds_ref').text(ref_seconds);
        }

        // ��ʼ���ü�������1�����1��
        var interval = setInterval(countdown, 1000);


        // �Ƿ���ʾ���룬 ע���������ڴ棬 ������ʹ��
        if(configs.milliShow){

            var ref_Milliseconds = 0;

            function Millisecond() {
                ref_Milliseconds++;

                // ֻ��ʾ��λ
                if(ref_Milliseconds > 100){
                    ref_Milliseconds = 0;
                    return;
                }
                container.find('.Milliseconds').text(ref_Milliseconds);
            }

            // ��ʼ���ü������� 1000��֮1�����һ��
            setInterval(Millisecond, 1);
        }
    };
})(jQuery);