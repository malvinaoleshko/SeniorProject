export default class Guide {
    constructor(json) {
        this.id = json['guide_id'];
        this.thumbnail = json['thumbnail'];
        this.date_last_modified = json['date_last_modified'];
        this.name = json['guide_name'];
        this.subscription_level = json['subscription_level'];
        this.favorite = json['fav'];
    }

    card() {
        return (
            '<div class="col-lg-3 col-md-6 col-sm-12">' + this.get_ribbon() +
                '<div class="small-box">' +
                    '<div class="inner" style="position: relative;">' +
                        '<svg class="overlay-button' + (this.favorite === 1 ? " favorite" : "") + '" ' +
                        'id="guide' + this.id + '" onclick="favorite(this)" viewBox="0 0 940.688 940.688">' +
                            '<path d="M885.344,319.071l-258-3.8l-102.7-264.399c-19.8-48.801-88.899-48.801-108.6,0l-102.7,264.399l-258,3.8\n' +
                            'c-53.4,3.101-75.1,70.2-33.7,103.9l209.2,181.4l-71.3,247.7c-14,50.899,41.1,92.899,86.5,65.899l224.3-122.7l224.3,122.601' +
                            'c45.4,27,100.5-15,86.5-65.9l-71.3-247.7l209.2-181.399C960.443,389.172,938.744,322.071,885.344,319.071z"/>' +
                        '</svg>' +
                        '<img src="' + this.thumbnail + '" alt="" class="img-fluid">' +
                    '</div>' +
                    '<a class="small-box-footer">' +
                        '<div class="row pl-1 pr-1">' +
                            '<div class="text-left col-6">' + this.name + '</div>' +
                            '<div class="text-right col-6">' + 'Added ' + this.date_str()  + '</div>' +
                        '</div>' +
                    '</a>' +
                '</div>' +
            '</div>'
        );
    }

    date_str() {
        const year_month_day = this.date_last_modified.split('-');
        const date = new Date(year_month_day[0], year_month_day[1] - 1, year_month_day[2]);
        const cur_date = new Date();
        const milli_in_day = 24 * 60 * 60 * 1000;
        const days_since = Math.floor(Math.abs(cur_date - date) / milli_in_day);
        if(days_since >= 365) {
            const years_since = cur_date.getFullYear() - date.getFullYear();
            return years_since.toString() + ((years_since > 1) ? (' years ago') : (' year ago'));
        }
        if(days_since > 30) {
            let months_since = cur_date.getMonth() - date.getMonth();
            if(months_since < 0) months_since = months_since + 12;
            return months_since.toString() + ((months_since > 1) ? (' months ago') : (' month ago'));
        }
        switch(days_since) {
            case 0:
                return 'today';
            case 1:
                return 'yesterday';
            default:
                return days_since.toString() + ' days ago';
        }
    }

    get_ribbon() {
        if(this.subscription_level === "WELCOME") return "";
        let color_levels = {
            "BEGINNER" : '<div class="ribbon bg-white">',
            "INTERMEDIATE" : '<div class="ribbon bg-yellow">',
            "ADVANCED" : '<div class="ribbon bg-red">',
            "PERSONAL" : '<div class="ribbon bg-black">'
        };
        return (
            '<div class="ribbon-wrapper ribbon-lg" style="right:5px">' +
                color_levels[this.subscription_level] +
                    this.subscription_level +
                '</div>' +
            '</div>'
        );
    }
};