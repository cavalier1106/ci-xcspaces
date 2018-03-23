var xcspaces = {

    SaveTopicsMenu: function (e, id, token_name, token_hash) {

        /*====================================
        Topics Menu 
        ======================================*/

        var TopicName = $(e).parent().find("input[name=\"TopicName\"]").val();
        var TopicTabName = $(e).parent().find("input[name=\"TopicTabName\"]").val();
        var Sort = $(e).parent('.media-body').find("input[name=\"sort\"]").val();
        var Status = $(e).parent('.media-body').find("select[name=\"status\"]").val();

        // console.log(Sort);
        // console.log(Status);

        var dt = {id:id,TopicName:TopicName,TopicTabName:TopicTabName,Sort:Sort,Status:Status,csrf:token_hash};

        $.ajax({
            url: '/xcspaceAdmin/SaveTopicsMenu',
            type: "POST",
            data: dt,
            dataType: "json",
            success: function(data){
                // console.log(data);
                location.href = "/xcspaceAdmin";
            }
        });

    },
    EditTopicsMenu: function (e) {

        /*====================================
        Topics Menu 
        ======================================*/

    },
    DelTopicsMenu: function (id, token_name, token_hash) {

        /*====================================
        Topics Menu 
        ======================================*/

        var dt = {id:id,csrf:token_hash};

        $.ajax({
            url: '/xcspaceAdmin/DelTopicsMenu',
            type: "POST",
            data: dt,
            dataType: "json",
            success: function(data){
                // console.log(data);
                location.href = "/xcspaceAdmin";
            }
        });

    },
    AddTopicsMenu: function (e, id, token_name, token_hash) {

        /*====================================
        Topics Menu 
        ======================================*/

        var TopicName = $(e).parent().find("#AddTopicName").val();
        var TopicTabName = $(e).parent().find("#AddTopicTabName").val();
        var Sort = $(e).parent('.media-body').find("#AddSort").val();
        var Status = $(e).parent('.media-body').find("#AddStatus").val();

        // console.log(Sort);
        // console.log(token_hash);

        var dt = {id:id,TopicName:TopicName,TopicTabName:TopicTabName,Sort:Sort,Status:Status,csrf:token_hash};

        $.ajax({
            url: '/xcspaceAdmin/AddTopicsMenu',
            type: "POST",
            data: dt,
            dataType: "json",
            success: function(data){
                // console.log(data);
                location.href = "/xcspaceAdmin";
            }
        });

    },
    AddParentTopicsMenu: function (e, token_name, token_hash) {

        /*====================================
        Topics Menu 
        ======================================*/

        var TopicName = $(e).parent().find("#AddParentTopicName").val();
        var TopicTabName = $(e).parent().find("#AddParentTopicTabName").val();
        var Sort = $(e).parent('.media-body').find("#AddParentSort").val();
        var Status = $(e).parent('.media-body').find("#AddParentStatus").val();

        var dt = {TopicName:TopicName,TopicTabName:TopicTabName,Sort:Sort,Status:Status,csrf:token_hash};

        $.ajax({
            url: '/xcspaceAdmin/AddParentTopicsMenu',
            type: "POST",
            data: dt,
            dataType: "json",
            success: function(data){
                // console.log(data);
                location.href = "/xcspaceAdmin";
            }
        });

    },

    
};