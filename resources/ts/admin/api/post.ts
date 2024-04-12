import axios from 'axios';

export interface PostRecord {
    content: string;
    images: string;
}
export interface PostCommentRecord {
    content: string;
}

export function posts(params) {
    return axios.get<PostRecord[]>('posts', {params});
}

export function destroyPosts(postId) {
    return axios.delete<null>('posts/' + postId);
}

export function postComments(params) {
    return axios.get<PostCommentRecord[]>('post-comments', {params});
}

export function destroyComments(commentId) {
    return axios.delete<null>('post-comments/' + commentId);
}


